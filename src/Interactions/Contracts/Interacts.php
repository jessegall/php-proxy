<?php

namespace JesseGall\Proxy\Interactions\Contracts;

use JesseGall\Proxy\Interactions\Status;

/**
 * @template T
 */
interface Interacts
{

    /**
     * @return T
     */
    public function getTarget(): object;

    /**
     * @param T $target
     * @return $this
     */
    public function setTarget(object $target): static;

    /**
     * @return Status
     */
    public function getStatus(): Status;

    /**
     * @param Status $status
     * @return $this
     */
    public function setStatus(Status $status): static;
    
}