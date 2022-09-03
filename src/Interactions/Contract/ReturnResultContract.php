<?php

namespace JesseGall\Proxy\Interactions\Contract;

interface ReturnResultContract
{

    public function getResult(): mixed;

    public function setResult(mixed $value): mixed;

}