<?php

namespace JesseGall\Proxy\Interactions\Contract;

interface ReturnsResultContract
{

    public function getResult(): mixed;

    public function setResult(mixed $value): mixed;

}