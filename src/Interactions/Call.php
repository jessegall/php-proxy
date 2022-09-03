<?php

namespace JesseGall\Proxy\Interactions;

class Call extends InteractionWithResult
{
    protected string $method;
    protected array $parameters;

    public function __construct(object $target, string $method, array $parameters)
    {
        parent::__construct($target);

        $this->method = $method;
        $this->parameters = $parameters;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function setMethod(string $method): static
    {
        $this->method = $method;

        return $this;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function setParameters(array $parameters): static
    {
        $this->parameters = $parameters;

        return $this;
    }

}