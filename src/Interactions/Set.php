<?php

namespace JesseGall\Proxy\Interactions;

class Set extends Interaction
{

    /**
     * The property of the target to set
     *
     * @var string
     */
    protected string $property;

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
     * @return string
     */
    public function getProperty(): string
    {
        return $this->property;
    }

    /**
     * @param string $property
     * @return $this
     */
    public function setProperty(string $property): static
    {
        $this->property = $property;

        return $this;
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