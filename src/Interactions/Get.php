<?php

namespace JesseGall\Proxy\Interactions;

class Get extends InteractionWithResult
{

    protected string $property;

    public function __construct(object $target, string $property)
    {
        parent::__construct($target);

        $this->property = $property;
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

}