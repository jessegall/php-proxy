<?php

namespace JesseGall\Proxy;

use JesseGall\Proxy\Contracts\HandlesFailedStrategies;
use JesseGall\Proxy\Interactions\Status;
use JesseGall\Proxy\Strategies\Exceptions\ExecutionException;

class ExceptionHandler
{

    /**
     * @var HandlesFailedStrategies[]
     */
    protected array $handlers = [];

    /**
     * Handles the failed strategy.
     * Sets the status of the interaction to failed
     *
     */
    public function handle(ExecutionException $exception): void
    {
        $interaction = $exception->getStrategy()->getInteraction();

        $interaction->setStatus(Status::FAILED);

        $this->callHandlers($exception);
    }

    /**
     * Register a failed strategy handler
     *
     * @param HandlesFailedStrategies $handler
     * @return ExceptionHandler
     */
    public function registerHandler(HandlesFailedStrategies $handler): static
    {
        $this->handlers[] = $handler;
    }

    /**
     * Calls the registered handlers
     *
     * @param ExecutionException $exception
     * @return void
     */
    protected function callHandlers(ExecutionException $exception): void
    {
        foreach ($this->handlers as $handler) {
            $handler->handle($exception);
        }
    }

}