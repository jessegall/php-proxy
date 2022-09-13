<?php

namespace JesseGall\Proxy;

class TargetArray
{

    private array $target;

    public function __construct(array &$target)
    {
        $this->target = &$target;
    }

}