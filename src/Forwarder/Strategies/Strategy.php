<?php

namespace JesseGall\Proxy\Forwarder\Strategies;

use JesseGall\Proxy\Forwarder\Strategies\Exceptions\ExecutionException;
use JesseGall\Proxy\Interactions\Contracts\Interacts;

/**
 * @template T of Interacts
 */
abstract class Strategy
{

    /**
     * @var T
     */
    protected readonly Interacts $interaction;

    /**
     * The result of the execution
     *
     * @var mixed
     */
    protected mixed $result = null;

    /**
     * The caller of the interaction
     *
     * @var object|null
     */
    protected ?object $caller;

    /**
     * @param T $interaction
     */
    public function __construct(Interacts $interaction, object $caller = null)
    {
        $this->interaction = $interaction;
        $this->caller = $caller;
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
    public function getInteraction(): Interacts
    {
        return $this->interaction;
    }

    /**
     * @return object|null
     */
    public function getCaller(): ?object
    {
        return $this->caller;
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
     * @return Strategy
     */
    public function setResult(mixed $result): Strategy
    {
        $this->result = $result;

        return $this;
    }

}