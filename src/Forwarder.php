<?php

namespace JesseGall\Proxy;

use JesseGall\Proxy\Contracts\Intercepts;
use JesseGall\Proxy\Exceptions\ForwardStrategyMissingException;
use JesseGall\Proxy\Interactions\CallInteraction;
use JesseGall\Proxy\Interactions\Contracts\InteractsAndReturnsResult;
use JesseGall\Proxy\Interactions\GetInteraction;
use JesseGall\Proxy\Interactions\Interaction;
use JesseGall\Proxy\Interactions\SetInteraction;
use JesseGall\Proxy\Interactions\Status;
use JesseGall\Proxy\Strategies\Exceptions\ExecutionException;
use JesseGall\Proxy\Strategies\ForwardCall;
use JesseGall\Proxy\Strategies\ForwardGet;
use JesseGall\Proxy\Strategies\ForwardSet;
use JesseGall\Proxy\Strategies\ForwardStrategy;

class Forwarder
{

    /**
     * Mapping of the forward strategies.
     *
     * @var array<class-string<Interaction>, class-string<ForwardStrategy>>
     */
    protected array $strategies = [
        CallInteraction::class => ForwardCall::class,
        GetInteraction::class => ForwardGet::class,
        SetInteraction::class => ForwardSet::class,
    ];

    /**
     * The list of registered interceptors.
     *
     * @var Intercepts[]
     */
    protected array $interceptors = [];

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
     * Register an interceptor.
     *
     * @param Intercepts|class-string<Intercepts>|class-string<Intercepts>[] $interceptor
     * @return void
     */
    public function registerInterceptor(Intercepts|string|array $interceptor): void
    {
        if (is_string($interceptor) || is_array($interceptor)) {
            $interceptor = (array)$interceptor;

            foreach ($interceptor as $type) {
                if (! is_subclass_of($type, Intercepts::class)) {
                    throw new \InvalidArgumentException('Type must be an instance of InterceptorContract');
                }

                $this->interceptors[] = new $type();
            }
        } else {
            $this->interceptors[] = $interceptor;
        }
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
    public function forward(Interaction $interaction): ConcludedInteraction
    {
        $this->notifyInterceptors($interaction);

        // Interceptors might change the status of the interaction.
        // That's why we check if the status is still pending after the interceptors are notified.
        // In the case that the interaction no longer has status pending, we skip the forwarding and return.
        if (! $interaction->hasStatus(Status::PENDING)) {
            return new ConcludedInteraction($interaction);
        }

        $strategy = $this->newForwardStrategy($interaction);

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
     * Create a new forward strategy for the given interaction.
     *
     * @param Interaction $interaction
     * @return ForwardStrategy
     * @throws ForwardStrategyMissingException
     */
    protected function newForwardStrategy(Interaction $interaction): ForwardStrategy
    {
        $type = $this->strategies[$interaction::class] ?? throw new ForwardStrategyMissingException($interaction);

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
     * @param array $interceptors
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
        $this->strategies = [];

        foreach ($strategies as $interaction => $strategy) {
            $this->setStrategy($interaction, $strategy);
        }

        return $this;
    }

    public function setStrategy(string $interaction, string $strategy): static
    {
        if (! is_subclass_of($interaction, Interaction::class)) {
            throw new \InvalidArgumentException('$interaction must be an instance of Interaction');
        }

        if (! is_subclass_of($strategy, ForwardStrategy::class)) {
            throw new \InvalidArgumentException('$strategy must be an instance of ForwardStrategy');
        }

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