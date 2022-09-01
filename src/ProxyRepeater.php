<?php

namespace JesseGall\Proxy;

use Closure;
use Exception;
use JesseGall\Proxy\Concerns\ForwardsInteractions;
use JesseGall\Proxy\Exceptions\MaxAttemptsExceededException;
use JesseGall\Proxy\Handlers\ProxyRepeaterHandler;

/**
 * @template T
 * @extends Proxy<T>
 */
class ProxyRepeater extends Proxy
{
    use ForwardsInteractions {
        forwardCallTo as private __forwardCallTo;
        forwardGetTo as private __forwardGetTo;
    }

    protected ProxyRepeaterHandler $handler;
    protected int $maxAttempts;
    protected int $attempts;

    /**
     * @param object $subject
     * @param Closure|null $handler
     * @param int $maxAttempts
     */
    public function __construct(object $subject, Closure $handler = null, int $maxAttempts = 3)
    {
        parent::__construct($subject);

        $this->handler = new ProxyRepeaterHandler($handler);
        $this->maxAttempts = $maxAttempts;
        $this->attempts = 0;
    }

    /**
     * @return Closure|null
     */
    public function getHandler(): ?Closure
    {
        return $this->handler;
    }

    /**
     * @return int
     */
    public function getMaxAttempts(): int
    {
        return $this->maxAttempts;
    }

    /**
     * @return int
     */
    public function getAttempts(): int
    {
        return $this->attempts;
    }

    /**
     * @throws Exception
     */
    protected function forwardCallTo(object $subject, string $method, $parameters): mixed
    {
        return $this->try(
            fn() => $this->__forwardCallTo($subject, $method, $parameters)
        );
    }

    /**
     * @throws Exception
     */
    protected function forwardGetTo(object $subject, string $property): mixed
    {
        return $this->try(
            fn() => $this->__forwardGetTo($subject, $property)
        );
    }

    /**
     * Runs the closure and catches the error if an exception is thrown.
     * If an exception is thrown and the handler is null or the handler returns true, repeat.
     *
     * @throws Exception
     */
    protected function try(Closure $closure)
    {
        try {
            return $closure();
        } catch (Exception $exception) {
            if (++$this->attempts >= $this->maxAttempts) {
                throw new MaxAttemptsExceededException();
            }

            $shouldRepeat = ($this->handler)($exception, $this);

            if ($shouldRepeat) {
                return $this->try($closure);
            }

            throw $exception;
        }
    }

}