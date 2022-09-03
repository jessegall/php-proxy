<?php

namespace JesseGall\Proxy\Strategies;

use JesseGall\Proxy\Interactions\Call;

/**
 * @extends ForwardStrategy<Call>
 */
class ForwardCall extends ForwardStrategy
{

    public function __construct(Call $interaction)
    {
        parent::__construct($interaction);
    }

    public function doExecute(): mixed
    {
        $target = $this->interaction->getTarget();
        $method = $this->interaction->getMethod();
        $params = $this->interaction->getParameters();

        return $target->{$method}(...$params);
    }

}