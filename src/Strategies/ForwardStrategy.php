<?php

namespace JesseGall\Proxy\Strategies;

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
     * Execute the strategy.
     * Set interaction status to fulfilled
     *
     * @return mixed
     */
    public function execute()
    {
        $result = $this->doExecute();

        $this->interaction->setStatus(Status::FULFILLED);

        return $result;
    }

    /**
     * Implementation of the strategy
     *
     * @return mixed
     */
    protected abstract function doExecute();

}