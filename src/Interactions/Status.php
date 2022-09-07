<?php

namespace JesseGall\Proxy\Interactions;

enum Status
{
    /**
     * Interaction is still waiting to be executed.
     */
    case PENDING;

    /**
     * Interaction is fulfilled.
     * An interaction will be marked fulfilled when the execution was successful.
     * Interceptors can mark an interaction as fulfilled to cancel the execution (e.g. A task was already completed).
     */
    case FULFILLED;

    /**
     * Interaction failed.
     * The exception handler will mark the interaction as failed when an exception is thrown while executing.
     */
    case FAILED;

    /**
     * Interaction was cancelled
     * An interaction can be marked cancelled by interceptors to cancel the execution.
     */
    case CANCELLED;

}
