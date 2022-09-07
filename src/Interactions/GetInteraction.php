<?php

namespace JesseGall\Proxy\Interactions;

use JesseGall\Proxy\Interactions\Concerns\HasProperty;
use JesseGall\Proxy\Interactions\Concerns\HasResult;
use JesseGall\Proxy\Interactions\Contracts\InteractsAndReturnsResult;
use JesseGall\Proxy\Interactions\Contracts\InteractsWithProperty;

class GetInteraction extends Interaction implements InteractsWithProperty, InteractsAndReturnsResult
{
    use HasProperty, HasResult;

    public function __construct(object $target, string $property)
    {
        parent::__construct($target);

        $this->property = $property;
    }
}