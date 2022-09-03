<?php

namespace JesseGall\Proxy\Interactions;

use JesseGall\Proxy\Contracts\HasResult;

abstract class InteractionWithResult extends Interaction implements HasResult
{

    /**
     * The result of this interaction
     *
     * @var mixed|null
     */
    protected mixed $result = null;

    /**
     * Get the result.
     * Return NULL when status is not fulfilled
     *
     * @return mixed
     */
    public function getResult(): mixed
    {
        if (! $this->hasStatus(Status::FULFILLED)) {
            return null;
        }

        return $this->result;
    }

    /**
     * @param mixed $result
     * @return $this
     */
    public function setResult(mixed $result): static
    {
        $this->result = $result;

        return $this;
    }

}