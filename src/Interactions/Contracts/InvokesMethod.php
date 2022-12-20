<?php

namespace JesseGall\Proxy\Interactions\Contracts;

interface InvokesMethod extends Interacts, WithResult
{

    public function getMethod(): string;

    public function setMethod(string $method): static;

    public function getParameters(): array;

    public function setParameters(array $parameters): static;

    public function getParameter(int $index): mixed;

}