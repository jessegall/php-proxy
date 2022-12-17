<?php

namespace JesseGall\Proxy\Forwarder;

use Closure;
use Exception;
use JesseGall\Proxy\ConcludedInteraction;
use JesseGall\Proxy\Forwarder\Contracts\HandlesFailedExecutions;
use JesseGall\Proxy\Forwarder\Contracts\Intercepts;
use JesseGall\Proxy\Forwarder\Exceptions\StrategyNullException;
use JesseGall\Proxy\Forwarder\Strategies\Exceptions\ExecutionException;
use JesseGall\Proxy\Forwarder\Strategies\Strategy;
use JesseGall\Proxy\HandlerContainer;
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
     * @var HandlerContainer<Intercepts>
     */
    protected HandlerContainer $interceptors;

    /**
     * The registered exception handlers
     *
     * @var HandlerContainer<HandlesFailedExecutions>
     */
    protected HandlerContainer $exceptionHandlers;


    public function __construct()
    {
        $this->factory = new StrategyFactory();
        $this->interceptors = new HandlerContainer();
        $this->exceptionHandlers = new HandlerContainer();
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
        $this->interceptors->call($interaction, $caller);

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
     * @param Intercepts|Closure|string|array $interceptor
     * @return void
     */
    public function registerInterceptor(Intercepts|Closure|string|array $interceptor): void
    {
        $this->interceptors->registerHandlers($interceptor);
    }

    /**
     * Register an exception handler.
     *
     * @param HandlesFailedExecutions|Closure|string|array $handler
     * @return void
     */
    public function registerExceptionHandler(HandlesFailedExecutions|Closure|string|array $handler): void
    {
        $this->exceptionHandlers->registerHandlers($handler);
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

        $this->exceptionHandlers->call($exception);

        if ($exception->shouldThrow()) {
            throw $exception->getException();
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
     * @return HandlerContainer
     */
    public function getInterceptors(): HandlerContainer
    {
        return $this->interceptors;
    }

    /**
     * @param HandlerContainer $interceptors
     * @return Forwarder
     */
    public function setInterceptors(HandlerContainer $interceptors): Forwarder
    {
        $this->interceptors = $interceptors;

        return $this;
    }

    /**
     * @return HandlerContainer
     */
    public function getExceptionHandlers(): HandlerContainer
    {
        return $this->exceptionHandlers;
    }

    /**
     * @param HandlerContainer $exceptionHandlers
     * @return Forwarder
     */
    public function setExceptionHandlers(HandlerContainer $exceptionHandlers): Forwarder
    {
        $this->exceptionHandlers = $exceptionHandlers;

        return $this;
    }

}