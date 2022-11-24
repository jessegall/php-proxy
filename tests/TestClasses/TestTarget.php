<?php

namespace Tests\TestClasses;

class TestTarget
{

    public string $property = 'value';

    public int $called = 0;

    public function method(): string
    {
        $this->called++;

        return 'value';
    }

    public function methodWithException(): void
    {
        $this->called++;

        throw new TestException();
    }

    public function getChild(): TestTarget
    {
        return new TestTarget();
    }

}