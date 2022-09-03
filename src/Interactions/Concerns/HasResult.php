<?php

namespace JesseGall\Proxy\Interactions\Concerns;

use JesseGall\Proxy\Interactions\Status;

trait HasResult
{

    protected mixed $result = null;

    public function getResult(): mixed
    {
        if (! $this->hasStatus(Status::FULFILLED)) {
            return null;
        }

        return $this->result;
    }

    public function setResult(mixed $result): static
    {
        $this->result = $result;

        return $this;
    }

}