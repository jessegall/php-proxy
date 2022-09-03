<?php

namespace JesseGall\Proxy\Interactions;

abstract class Interaction
{

    protected object $target;
    protected Status $status;

    public function __construct(object $target)
    {
        $this->target = $target;
        $this->status = Status::PENDING;
    }

    public function getTarget(): object
    {
        return $this->target;
    }

    public function setTarget(object $target): static
    {
        $this->target = $target;

        return $this;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function setStatus(Status $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function hasStatus(Status $status): bool
    {
        return $this->status === $status;
    }

}