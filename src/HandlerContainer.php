<?php

namespace JesseGall\Proxy;

use Closure;
use JesseGall\Proxy\Contracts\Handler;

/**
 * @template T
 */
class HandlerContainer
{

    /**
     * @var Handler[]
     */
    private array $handlers = [];

    /**
     * @param mixed $items
     * @return void
     */
    public function registerHandlers(mixed $items): void
    {
        if (! is_array($items)) {
            $items = [$items];
        }

        foreach ($items as $handler) {
            if ($handler instanceof Closure) {
                $handler = new ClosureDelegate($handler);
            } elseif (is_string($handler)) {
                $handler = new $handler;
            }

            $this->handlers[] = $handler;
        }
    }

    /**
     * Call handlers
     *
     * @param ...$args
     * @return void
     */
    public function call(...$args): void
    {
        foreach ($this->handlers as $handler) {
            $handler->handle(...$args);
        }
    }

    /**
     * Call handlers
     *
     * @return void
     */
    public function clear(): void
    {
        $this->handlers = [];
    }

    /**
     * @return Handler[]
     */
    public function getHandlers(): array
    {
        return $this->handlers;
    }


}