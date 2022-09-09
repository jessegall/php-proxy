<?php

namespace JesseGall\Proxy\Interactions;

use JesseGall\Proxy\Interactions\Concerns\HasProperty;
use JesseGall\Proxy\Interactions\Concerns\HasValue;
use JesseGall\Proxy\Interactions\Contracts\InteractsWithAndModifiesProperty;

class SetInteraction extends Interaction implements InteractsWithAndModifiesProperty
{
    use HasProperty, HasValue;

    public function __construct(object $target, string $property, mixed $value)
    {
        parent::__construct($target);

        $this->property = $property;
        $this->value = $value;
    }

}