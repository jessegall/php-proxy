<?php

namespace JesseGall\Proxy\Interactions;

use JesseGall\Proxy\InteractionHash;
use JesseGall\Proxy\Interactions\Contracts\Interacts;

/**
 * @template  T
 * @implements Interacts<T>
 */
abstract class Interaction implements Interacts
{

    /**
     * The target of the interaction
     *
     * @var T
     */
    protected object $target;

    /**
     * The status of the interaction
     *
     * @var Status
     */
    protected Status $status;

    /**
     * @param T $target
     */
    public function __construct(object $target)
    {
        $this->target = $target;
        $this->status = Status::PENDING;
    }

    /**
     * @return T
     */
    public function getTarget(): object
    {
        return $this->target;
    }

    /**
     * @param T $target
     * @return $this
     */
    public function setTarget(object $target): static
    {
        $this->target = $target;

        return $this;
    }

    /**
     * @return Status
     */
    public function getStatus(): Status
    {
        return $this->status;
    }

    /**
     * @param Status $status
     * @return $this
     */
    public function setStatus(Status $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @param Status $status
     * @return bool
     */
    public function hasStatus(Status $status): bool
    {
        return $this->status === $status;
    }

    /**
     * @return string
     */
    public function toHash(): string
    {
        return (new InteractionHash($this))->generate();
    }

}