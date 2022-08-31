<?php

namespace JesseGall\Proxy\Exceptions;

use Exception;

class MaxAttemptsExceededException extends Exception
{

    public function __construct()
    {
        parent::__construct("The maximum amount of attempts exceeded");
    }

}