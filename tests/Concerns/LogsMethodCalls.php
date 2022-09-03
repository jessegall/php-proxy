<?php

namespace Test\Concerns;

use function PHPUnit\Framework\assertEquals;

trait LogsMethodCalls
{

    public array $calls = [];
    public array $results = [];

    private function logMethodCall(string $method): mixed
    {
        if (! array_key_exists($method, $this->calls)) {
            $this->calls[$method] = 0;
        }

        $this->calls[$method]++;

        return $this->results[$method] ?? null;
    }

    public function assertCalled(string $method, int $times = 1): void
    {
        assertEquals($times, $this->callCount($method));
    }

    public function callCount(string $method): int
    {
        return $this->calls[$method] ?? 0;
    }

}