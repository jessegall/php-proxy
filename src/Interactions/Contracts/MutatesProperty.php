<?php

namespace JesseGall\Proxy\Interactions\Contracts;

interface MutatesProperty extends Interacts
{

    public function getProperty(): string;

    public function getValue(): mixed;

    public function setValue(mixed $value);

}