<?php

namespace JesseGall\Proxy\Interactions\Contracts;

interface InteractsWithProperty extends Interacts
{

    public function getProperty(): string;

    public function setProperty(string $property): static;

}