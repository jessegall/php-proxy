<?php

namespace JesseGall\Proxy\Interactions\Concerns;

trait HasProperty
{

    /**
     * The property to interact with
     *
     * @var string
     */
    protected string $property;

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

}