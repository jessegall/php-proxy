<?php

namespace JesseGall\Proxy;

class TargetArray implements \ArrayAccess
{

    private array $target;

    public function __construct(array $target)
    {
        $this->target = $target;
    }

    public function __get(string $property)
    {
        return $this->target[$property];
    }

    public function __set(string $property, mixed $value): void
    {
        $this->target[$property] = $value;
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->target[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->target[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->target[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->target[$offset]);
    }

}