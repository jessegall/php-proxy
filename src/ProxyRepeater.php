<?php

namespace JesseGall\Proxy;

use Closure;
use Exception;
use JesseGall\Proxy\Exceptions\MaxAttemptsExceededException;

/**
 * @template T
 * @extends Proxy<T>
 */
class ProxyRepeater extends Proxy
{

    protected ?Closure $handler;
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

        $this->handler = $handler;
        $this->maxAttempts = $maxAttempts;
        $this->attempts = 0;
    }

    /**
     * @throws Exception
     */
    public function __call(string $method, array $parameters): mixed
    {
        return $this->try(
            fn() => parent::__call($method, $parameters),
        );
    }

    /**
     * @throws Exception
     */
    public function __get(string $name): mixed
    {
        return $this->try(
            fn() => parent::__get($name),
        );
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

            $shouldRepeat = is_null($this->handler) || ($this->handler)($exception, $this);

            if ($shouldRepeat) {
                return $this->try($closure);
            }

            throw $exception;
        }
    }

}