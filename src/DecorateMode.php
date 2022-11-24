<?php

namespace JesseGall\Proxy;

enum DecorateMode
{

    /**
     * Never wrap the result in proxy
     */
    case NEVER;

    /**
     * Only wrap the result in proxy when the value is the target
     */
    case EQUALS;

    /**
     * Always wrap the result in proxy when the value is an object
     */
    case ALWAYS;

}