<?php

namespace JesseGall\Proxy;

use Closure;
use JesseGall\Proxy\Contracts\HandlesFailedStrategies;
use JesseGall\Proxy\Contracts\Intercepts;
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
     * @var array<class-string<Interaction>, class-string<ForwardStrategy>>
     */
    protected array $strategies = [
        CallInteraction::class => CallStrategy::class,
        GetInteraction::class => GetStrategy::class,
        SetInteraction::class => SetStrategy::class,
    ];

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

    /**
     * The exception handler.
     *
     * @var ExceptionHandler
     */
    protected ExceptionHandler $exceptionHandler;

    public function __construct()
    {
        $this->exceptionHandler = new ExceptionHandler();
    }

    /**
     * Forward the interaction to the target and return concluded interaction.
     * Before forwarding notify interceptors about the interaction.
     * Cancel forwarding when the status is not pending.
     *
     * @param Interaction $interaction
     * @return ConcludedInteraction
     * @throws ForwardStrategyMissingException
     */
    public function forward(Interacts $interaction): ConcludedInteraction
    {
        $this->notifyInterceptors($interaction);

        // Interceptors might change the status of the interaction.
        // That's why we check if the status is still pending after the interceptors are notified.
        // In the case that the interaction no longer has status pending, we skip the forwarding and return.
        if (! $interaction->hasStatus(Status::PENDING)) {
            return new ConcludedInteraction($interaction);
        }

        $strategy = $this->newStrategy($interaction);

        $this->tryExecuting($strategy);

        if ($interaction instanceof InteractsAndReturnsResult) {
            $interaction->setResult($strategy->getResult());
        }

        $interaction->setStatus(Status::FULFILLED);

        return new ConcludedInteraction($interaction);
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
            $this->exceptionHandler->handle($exception);
        }
    }

    /**
     * Register an interceptor.
     *
     * @param Intercepts|Closure|class-string<Intercepts>|Closure[]|class-string<Intercepts>[] $interceptor
     * @return void
     */
    public function registerInterceptor(Intercepts|Closure|string|array $interceptor): void
    {
        if (! is_array($interceptor)) {
            $interceptor = [$interceptor];
        }

        foreach ($interceptor as $item) {
            if ($item instanceof Closure) {
                $item = new ClosureInterceptor($item);
            } elseif (is_string($item)) {
                $item = new $item;
            }

            $this->interceptors[] = $item;
        }
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
     * Create a new forward strategy for the given interaction.
     *
     * @param Interaction $interaction
     * @return ForwardStrategy
     * @throws ForwardStrategyMissingException
     */
    protected function newStrategy(Interaction $interaction): ForwardStrategy
    {
        $type = $this->getStrategy(get_class($interaction));

        if (is_null($type)) {
            throw new ForwardStrategyMissingException($interaction);
        }

        return new $type($interaction);
    }

    /**
     * Notify interceptors about an incoming interaction.
     *
     * @param Interaction $interaction
     * @return void
     */
    protected function notifyInterceptors(Interaction $interaction): void
    {
        foreach ($this->interceptors as $interceptor) {
            $interceptor->intercept($interaction);
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
     * @return array<class-string<Interaction>, class-string<ForwardStrategy>>
     */
    public function getStrategies(): array
    {
        return $this->strategies;
    }

    /**
     * @param array<class-string<Interaction>, class-string<ForwardStrategy>> $strategies
     * @return Forwarder
     */
    public function setStrategies(array $strategies): static
    {
        $this->strategies = $strategies;

        return $this;
    }

    /**
     * Get the strategy for a specific interaction type
     *
     * @param class-string<Intercepts> $interception
     * @return string|null
     */
    public function getStrategy(string $interception): ?string
    {
        return $this->strategies[$interception] ?? null;
    }

    /**
     * Sets a strategy for a specific interaction type
     *
     * @param callable-string<Intercepts> $interaction
     * @param class-string<ForwardStrategy> $strategy
     * @return $this
     */
    public function setStrategy(string $interaction, string $strategy): static
    {
        $this->strategies[$interaction] = $strategy;

        return $this;
    }

    /**
     * @return ExceptionHandler
     */
    public function getExceptionHandler(): ExceptionHandler
    {
        return $this->exceptionHandler;
    }

    /**
     * @param ExceptionHandler $exceptionHandler
     * @return Forwarder
     */
    public function setExceptionHandler(ExceptionHandler $exceptionHandler): static
    {
        $this->exceptionHandler = $exceptionHandler;

        return $this;
    }

}