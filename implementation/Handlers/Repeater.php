<?php

namespace JesseGall\Proxy\Implementation\Handlers;

use JesseGall\Proxy\Contracts\HandlesFailedStrategies;
use JesseGall\Proxy\Implementation\Handlers\Exceptions\MaxAttemptsExceededException;
use JesseGall\Proxy\Strategies\Exceptions\ExecutionException;

class Repeater implements HandlesFailedStrategies
{

    private int $maxAttempts;
    private int $attempts;

    public function __construct(int $maxAttempts = 5)
    {
        $this->maxAttempts = $maxAttempts;
        $this->attempts = 0;
    }

    /**
     * @throws MaxAttemptsExceededException
     */
    public function handle(ExecutionException $exception)
    {
        while (true) {
            ++$this->attempts;

            try {
                $exception->getStrategy()->execute();

                return;
            } catch (ExecutionException $exception) {
                if ($this->attempts >= $this->maxAttempts) {
                    throw new MaxAttemptsExceededException($exception);
                }
            }
        }
    }

}