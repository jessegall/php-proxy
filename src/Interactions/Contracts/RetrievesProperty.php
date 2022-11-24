<?php

namespace JesseGall\Proxy\Interactions\Contracts;

interface RetrievesProperty extends Interacts, WithResult
{

    public function getProperty(): string;

}