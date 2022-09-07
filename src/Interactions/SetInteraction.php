<?php

namespace JesseGall\Proxy\Interactions;

use JesseGall\Proxy\Interactions\Concerns\HasProperty;
use JesseGall\Proxy\Interactions\Contracts\InteractsWithProperty;

class SetInteraction extends Interaction implements InteractsWithProperty
{
    use HasProperty;

    /**
     * The value to apply to the property
     *
     * @var mixed
     */
    protected mixed $value;

    public function __construct(object $target, string $property, mixed $value)
    {
        parent::__construct($target);

        $this->property = $property;
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function setValue(mixed $value): static
    {
        $this->value = $value;

        return $this;
    }
}