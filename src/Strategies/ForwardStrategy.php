<?php

namespace JesseGall\Proxy\Strategies;

use JesseGall\Proxy\Interactions\Contracts\Interacts;
use JesseGall\Proxy\Interactions\Contracts\InteractsAndReturnsResult;
use JesseGall\Proxy\Interactions\Interaction;
use JesseGall\Proxy\Strategies\Exceptions\ExecutionException;

/**
 * @template T of Interacts
 */
abstract class ForwardStrategy
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
    private ?object $caller;

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
     * @return ForwardStrategy
     */
    public function setResult(mixed $result): ForwardStrategy
    {
        $this->result = $result;

        return $this;
    }

}