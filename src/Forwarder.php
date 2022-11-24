<?php

namespace JesseGall\Proxy;

use Closure;
use Exception;
use JesseGall\Proxy\Contracts\HandlesFailedStrategies;
use JesseGall\Proxy\Contracts\Intercepts;
use JesseGall\Proxy\Contracts\ResolvesForwardStrategy;
use JesseGall\Proxy\Exceptions\ForwardStrategyMissingException;
use JesseGall\Proxy\Interactions\CallInteraction;
use JesseGall\Proxy\Interactions\Contracts\Interacts;
use JesseGall\Proxy\Interactions\Contracts\InteractsAndReturnsResult;
use JesseGall\Proxy\Interactions\GetInteraction;
use JesseGall\Proxy\Interactions\Interaction;
use JesseGall\Proxy\Interactions\SetInteraction;
use JesseGall\Proxy\Interactions\Status;
use JesseGall\Proxy\Strategies\CallStrategy;
use JesseGall\Proxy\Strategies\Exceptions\ExecutionException;
use JesseGall\Proxy\Strategies\ForwardStrategy;
use JesseGall\Proxy\Strategies\GetStrategy;
use JesseGall\Proxy\Strategies\SetStrategy;

class Forwarder
{

    /**
     * Mapping of the forward strategies.
     *
     * @var array<class-string<\JesseGall\Proxy\Interactions\Interaction>, ResolvesForwardStrategy>
     */
    protected array $strategyResolvers = [];

    /**
     * The registered interceptors.
     *
     * @var Intercepts[]
     */
    protected array $interceptors = [];

    /**
     * The registered exception handlers
     *
     * @var HandlesFailedStrategies[]
     */
    protected array $exceptionHandlers = [];

    public function __construct()
    {
        $this->setStrategyResolver(CallInteraction::class, new ForwardStrategyResolver(CallStrategy::class));
        $this->setStrategyResolver(GetInteraction::class, new ForwardStrategyResolver(GetStrategy::class));
        $this->setStrategyResolver(SetInteraction::class, new ForwardStrategyResolver(SetStrategy::class));
    }

    /**
     * Forward the interaction to the target and return concluded interaction.
     * Before forwarding notify interceptors about the interaction.
     * Cancel forwarding when the status is not pending.
     *
     * @param Interaction $interaction
     * @param object|null $caller
     * @return ConcludedInteraction
     */
    public function forward(Interacts $interaction, object $caller = null): ConcludedInteraction
    {
        $this->notifyInterceptors($interaction, $caller);

        // Interceptors might change the status of the interaction.
        // That's why we check if the status is still pending after the interceptors are notified.
        // In the case that the interaction no longer has status pending, we skip the forwarding and return.
        if (! $interaction->hasStatus(Status::PENDING)) {
            return new ConcludedInteraction($interaction, $caller);
        }

        $strategy = $this->newStrategy($interaction, $caller);

        $this->tryExecuting($strategy);

        if ($interaction instanceof InteractsAndReturnsResult) {
            $interaction->setResult($strategy->getResult());
        }

        $interaction->setStatus(Status::FULFILLED);

        return new ConcludedInteraction($interaction, $caller);
    }

    /**
     * Register an interceptor.
     *
     * @param Intercepts|Closure|class-string<\JesseGall\Proxy\Contracts\Intercepts>|Closure[]|class-string<\JesseGall\Proxy\Contracts\Intercepts>[] $interceptor
     * @return void
     */
    public function registerInterceptor(Intercepts|Closure|string|array $interceptor): void
    {
        $this->registerItems($interceptor, ClosureInterceptor::class, $this->interceptors);
    }

    /**
     * Removes all the interceptors
     *
     * @return void
     */
    public function clearInterceptors(): void
    {
        $this->interceptors = [];
    }

    /**
     * Register an exception handler.
     *
     * @param HandlesFailedStrategies|Closure|class-string<\JesseGall\Proxy\Contracts\HandlesFailedStrategies>|Closure[]|class-string<\JesseGall\Proxy\Contracts\HandlesFailedStrategies>[] $handler
     * @return void
     */
    public function registerExceptionHandler(HandlesFailedStrategies|Closure|string|array $handler): void
    {
        $this->registerItems($handler, ClosureHandler::class, $this->exceptionHandlers);
    }

    /**
     * Removes all the exception handlers
     *
     * @return void
     */
    public function clearExceptionHandlers(): void
    {
        $this->exceptionHandlers = [];
    }

    /**
     * Register an item with the target
     * If an item is a closure, wrap the closure in the given delegate class
     *
     * @param mixed $items
     * @param class-string<\JesseGall\Proxy\ClosureDelegate> $delegate
     * @param array $target
     * @return void
     */
    protected function registerItems(mixed $items, string $delegate, array &$target): void
    {
        if (! is_array($items)) {
            $items = [$items];
        }

        foreach ($items as $item) {
            if ($item instanceof Closure) {
                $item = new $delegate($item);
            } elseif (is_string($item)) {
                $item = new $item;
            }

            $target[] = $item;
        }
    }

    /**
     * Try to execute the given strategy.
     * Forwards any thrown exceptions to the exception handler.
     *
     * @param ForwardStrategy $strategy
     */
    protected function tryExecuting(ForwardStrategy $strategy): void
    {
        try {
            $strategy->execute();
        } catch (ExecutionException $exception) {
            $this->handleException($exception);
        }
    }

    /**
     * @param ExecutionException $exception
     * @return void
     * @throws Exception
     */
    protected function handleException(ExecutionException $exception): void
    {
        $interaction = $exception->getStrategy()->getInteraction();

        $interaction->setStatus(Status::FAILED);

        $this->callExceptionHandlers($exception);

        if ($exception->shouldThrow()) {
            throw $exception->getException();
        }
    }

    /**
     * Create a new forward strategy for the given interaction.
     *
     * @param Interaction $interaction
     * @param object|null $caller
     * @return ForwardStrategy
     */
    protected function newStrategy(Interacts $interaction, object $caller = null): ForwardStrategy
    {
        $class = get_class($interaction);

        if (! array_key_exists($class, $this->strategyResolvers)) {
            throw new ForwardStrategyMissingException($interaction);
        }

        $resolver = $this->strategyResolvers[$class];

        return $resolver->resolve($interaction, $caller);
    }

    /**
     * Notify interceptors about an incoming interaction.
     *
     * @param Interacts $interaction
     * @param object|null $caller
     * @return void
     */
    protected function notifyInterceptors(Interacts $interaction, object $caller = null): void
    {
        foreach ($this->interceptors as $interceptor) {
            $interceptor->intercept($interaction, $caller);
        }
    }

    /**
     * Calls the registered exception handlers
     *
     * @param ExecutionException $exception
     * @return void
     */
    protected function callExceptionHandlers(ExecutionException $exception): void
    {
        foreach ($this->exceptionHandlers as $handler) {
            $handler->handle($exception);
        }
    }

    # --- Getters and Setters ---

    /**
     * @return Intercepts[]
     */
    public function getInterceptors(): array
    {
        return $this->interceptors;
    }

    /**
     * @param Intercepts[] $interceptors
     * @return $this
     */
    public function setInterceptors(array $interceptors): static
    {
        $this->interceptors = $interceptors;

        return $this;
    }

    /**
     * @return array<class-string<\Jessegall\Proxy\Interactions\Interaction>, class-string<\JesseGall\Proxy\Strategies\ForwardStrategy>>
     */
    public function getStrategyResolvers(): array
    {
        return $this->strategyResolvers;
    }

    /**
     * @param array<class-string<\Jessegall\Proxy\Interactions\Interaction>, Closure> $strategyResolvers
     * @return Forwarder
     */
    public function setStrategyResolvers(array $strategyResolvers): static
    {
        $this->strategyResolvers = $strategyResolvers;

        return $this;
    }

    /**
     * Sets a strategy for a specific interaction type
     *
     * @param callable-string<\JesseGall\Proxy\Contracts\Intercepts> $interaction
     * @param ForwardStrategyResolver $resolver
     * @return $this
     */
    public function setStrategyResolver(string $interaction, ForwardStrategyResolver $resolver): static
    {
        $this->strategyResolvers[$interaction] = $resolver;

        return $this;
    }

    /**
     * Get the registered exception handlers
     *
     * @return array
     */
    public function getExceptionHandlers(): array
    {
        return $this->exceptionHandlers;
    }

    /**
     * Set the exception handlers
     *
     * @param array $exceptionHandlers
     * @return Forwarder
     */
    public function setExceptionHandlers(array $exceptionHandlers): Forwarder
    {
        $this->exceptionHandlers = $exceptionHandlers;

        return $this;
    }

}