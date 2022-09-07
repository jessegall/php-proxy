<?php

namespace JesseGall\Proxy\Interactions\Concerns;

trait HasMethodAndParameters
{

    /**
     * The method to interact with
     *
     * @var string
     */
    protected string $method;

    /**
     * The parameters to pass to the method
     *
     * @var array
     */
    protected array $parameters;

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @param string $method
     * @return $this
     */
    public function setMethod(string $method): static
    {
        $this->method = $method;

        return $this;
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     * @return $this
     */
    public function setParameters(array $parameters): static
    {
        $this->parameters = $parameters;

        return $this;
    }
}