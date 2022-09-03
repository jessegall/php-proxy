<?php

namespace JesseGall\Proxy\Interactions;

use JesseGall\Proxy\Interactions\Concerns\HasResult;
use JesseGall\Proxy\Interactions\Contract\ReturnResultContract;

class Get extends Interaction implements ReturnResultContract
{
    use HasResult;

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