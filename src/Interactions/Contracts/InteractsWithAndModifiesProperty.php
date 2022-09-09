<?php

namespace JesseGall\Proxy\Interactions\Contracts;

interface InteractsWithAndModifiesProperty extends InteractsWithProperty
{

    public function getValue(): mixed;

    public function setValue(mixed $value);

}