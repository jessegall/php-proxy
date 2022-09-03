<?php

namespace JesseGall\Proxy\Strategies;

use JesseGall\Proxy\Interactions\Get;

/**
 * @extends ForwardStrategy<Get>
 */
class ForwardGet extends ForwardStrategy
{

    public function doExecute(): mixed
    {
        $target = $this->interaction->getTarget();
        $property = $this->interaction->getProperty();

        return $target->{$property};
    }

}