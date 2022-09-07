<?php

namespace JesseGall\Proxy\Interactions\Contracts;

interface InteractsAndReturnsResult
{

    public function getResult(): mixed;

    public function setResult(mixed $result): static;

}