<?php

namespace JesseGall\Proxy\Contracts;

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
     * @return Status
     */
    public function getStatus(): Status;

}