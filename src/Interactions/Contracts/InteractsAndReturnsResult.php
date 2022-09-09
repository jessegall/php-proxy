<?php

namespace JesseGall\Proxy\Interactions\Contracts;

interface InteractsAndReturnsResult extends Interacts
{

    public function getResult(): mixed;

    public function setResult(mixed $result): static;

}