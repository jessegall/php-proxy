<?php

namespace JesseGall\Proxy\Strategies;

use JesseGall\Proxy\Contracts\HasResult;
use JesseGall\Proxy\Interactions\Interaction;
use JesseGall\Proxy\Interactions\Status;

/**
 * @template T of Interaction
 */
abstract class ForwardStrategy
{

    /**
     * @var T
     */
    protected readonly Interaction $interaction;

    /**
     * @param T $interaction
     */
    public function __construct(Interaction $interaction)
    {
        $this->interaction = $interaction;
    }

    /**
     * Execute the strategy and return result
     */
    public function execute()
    {
        return $this->doExecute();
    }

    /**
     * Implementation of the strategy
     *
     * @return mixed
     */
    protected abstract function doExecute();

}