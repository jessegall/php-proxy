<?php

namespace JesseGall\Proxy\Strategies;

use JesseGall\Proxy\Interactions\Contracts\InteractsAndReturnsResult;
use JesseGall\Proxy\Interactions\Interaction;
use JesseGall\Proxy\Strategies\Exceptions\ExecutionException;

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
     * The result of the execution
     *
     * @var mixed
     */
    protected mixed $result;

    /**
     * @param T $interaction
     */
    public function __construct(Interaction $interaction)
    {
        $this->interaction = $interaction;
    }

    /**
     * Execute the strategy
     *
     * @return void
     * @throws ExecutionException
     */
    public function execute(): void
    {
        try {
            $this->result = $this->doExecute();
        } catch (\Exception $exception) {
            throw new ExecutionException($this, $exception);
        }
    }

    /**
     * Implementation of the strategy
     *
     * @return mixed
     */
    abstract protected function doExecute();

    # --- Getters and Setters ---

    /**
     * @return T
     */
    public function getInteraction(): Interaction
    {
        return $this->interaction;
    }

    /**
     * @return mixed
     */
    public function getResult(): mixed
    {
        return $this->result;
    }

    /**
     * @param mixed $result
     * @return ForwardStrategy
     */
    public function setResult(mixed $result): ForwardStrategy
    {
        $this->result = $result;

        return $this;
    }

}