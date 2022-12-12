<?php

namespace JesseGall\Proxy;


/**
 * This is a PHP enum definition for a DecorateMode class,
 * which provides a set of constants that can be used to determine when a result should be wrapped in a Proxy object.
 */
enum DecorateMode
{

    /**
     * Indicates that the result should never be wrapped in a Proxy object.
     */
    case NEVER;

    /**
     * Indicates that the result should only be wrapped in a Proxy object if it is equal to the target object of the Proxy
     */
    case EQUALS_TARGET;

    /**
     * Indicates that the result should always be wrapped in a Proxy object if it is an object.
     */
    case ALWAYS;

}