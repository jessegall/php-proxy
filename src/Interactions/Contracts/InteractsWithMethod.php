<?php

namespace JesseGall\Proxy\Interactions\Contracts;

interface InteractsWithMethod extends Interacts
{

    public function getMethod(): string;

    public function setMethod(string $method): static;

    public function getParameters(): array;

    public function setParameters(array $parameters): static;

}