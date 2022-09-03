<?php

namespace JesseGall\Proxy\Contracts;

interface HasResult
{

    public function getResult(): mixed;

    public function setResult(mixed $value): mixed;

}