<?php

namespace JesseGall\Proxy\Interactions;

class Set extends Interaction
{

    protected string $property;
    protected mixed $value;

    public function __construct(object $target, string $property, mixed $value)
    {
        parent::__construct($target);

        $this->property = $property;
        $this->value = $value;
    }

    public function getProperty(): string
    {
        return $this->property;
    }

    public function setProperty(string $property): static
    {
        $this->property = $property;

        return $this;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function setValue(mixed $value): static
    {
        $this->value = $value;
        return $this;
    }
}