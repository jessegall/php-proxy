<?php

namespace Test\TestClasses;

use JesseGall\Proxy\Interactions\Contracts\InteractsAndReturnsResult;

class TestInteractionWithResult extends TestInteraction implements InteractsAndReturnsResult
{

    private mixed $result;

    public function __construct(mixed $result = null)
    {
        parent::__construct();

        $this->result = $result;
    }

    public function getResult(): mixed
    {
        return $this->result;
    }

    public function setResult(mixed $result): static
    {
        $this->result = $result;

        return $this;
    }
}