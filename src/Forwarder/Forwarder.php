<?php

namespace JesseGall\Proxy\Forwarder;

use Closure;
use Exception;
use JesseGall\Proxy\ClosureHandler;
use JesseGall\Proxy\ClosureInterceptor;
use JesseGall\Proxy\ConcludedInteraction;
use JesseGall\Proxy\Contracts\HandlesFailedStrategies;
use JesseGall\Proxy\Contracts\Intercepts;
use JesseGall\Proxy\Forwarder\Exceptions\StrategyNullException;
use JesseGall\Proxy\Forwarder\Strategies\Exceptions\ExecutionException;
use JesseGall\Proxy\Forwarder\Strategies\Strategy;
use JesseGall\Proxy\Interactions\Contracts\Interacts;
use JesseGall\Proxy\Interactions\Contracts\WithResult;
use JesseGall\Proxy\Interactions\Interaction;
use JesseGall\Proxy\Interactions\Status;

class Forwarder
{

    /**
     * The strategy factory.
     *
     * @var StrategyFactory
     */
    protected StrategyFactory $factory;

    /**
     * The registered interceptors.
     *
     * @var Intercepts[]
     */
    protected array $interceptors;

    /**
     * The registered exception handlers
     *
     * @var HandlesFailedStrategies[]
     */
    protected array $exceptionHandlers;


    public function __construct()
    {
        $this->factory = new StrategyFactory();
        $this->interceptors = [];
        $this->exceptionHandlers = [];
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
        if ($interaction->hasStatus(Status::PENDING)) {
            $strategy = $this->factory->make($interaction, $caller);

            if (is_null($strategy)) {
                throw new StrategyNullException($interaction);
            }

            $this->tryExecuting($strategy);

            if ($interaction instanceof WithResult) {
                $interaction->setResult($strategy->getResult());
            }

            $interaction->setStatus(Status::FULFILLED);
        }

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
     * @param Strategy $strategy
     */
    protected function tryExecuting(Strategy $strategy): void
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
     * @return StrategyFactory
     */
    public function getFactory(): StrategyFactory
    {
        return $this->factory;
    }

    /**
     * @param StrategyFactory $factory
     * @return Forwarder
     */
    public function setFactory(StrategyFactory $factory): Forwarder
    {
        $this->factory = $factory;

        return $this;
    }

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
     * @return array<class-string<\Jessegall\Proxy\Interactions\Interaction>, class-string<\JesseGall\Proxy\Forwarder\Strategies\Strategy>>
     */
    public function getFactories(): array
    {
        return $this->factories;
    }

    /**
     * @param array<class-string<\Jessegall\Proxy\Interactions\Interaction>, Closure> $factories
     * @return Forwarder
     */
    public function setFactories(array $factories): static
    {
        $this->factories = $factories;

        return $this;
    }

    /**
     * Sets a strategy for a specific interaction type
     *
     * @param callable-string<\JesseGall\Proxy\Contracts\Intercepts> $interaction
     * @param StrategyFactory $factory
     * @return $this
     */
    public function registerStrategyFactory(string $interaction, StrategyFactory $factory): static
    {
        $this->factories[$interaction] = $factory;

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