<?php

namespace JesseGall\Proxy\Interactions\Concerns;

trait HasResults
{

    protected mixed $result;

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