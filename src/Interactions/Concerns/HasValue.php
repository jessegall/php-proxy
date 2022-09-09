<?php

namespace JesseGall\Proxy\Interactions\Concerns;

trait HasValue
{
    /**
     * The value to apply to the property
     *
     * @var mixed
     */
    protected mixed $value;

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