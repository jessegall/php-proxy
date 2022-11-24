<?php

namespace JesseGall\Proxy\Interactions;

use JesseGall\Proxy\Interactions\Concerns\HasProperty;
use JesseGall\Proxy\Interactions\Concerns\HasResult;
use JesseGall\Proxy\Interactions\Contracts\WithResult;
use JesseGall\Proxy\Interactions\Contracts\RetrievesProperty;

class GetInteraction extends Interaction implements RetrievesProperty
{
    use HasProperty, HasResult;

    public function __construct(object $target, string $property)
    {
        parent::__construct($target);

        $this->property = $property;
    }
}