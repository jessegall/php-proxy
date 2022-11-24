<?php

namespace JesseGall\Proxy;

use JesseGall\Proxy\Interactions\Contracts\Interacts;
use JesseGall\Proxy\Interactions\Contracts\InvokesMethod;
use JesseGall\Proxy\Interactions\Contracts\MutatesProperty;
use JesseGall\Proxy\Interactions\Contracts\RetrievesProperty;

class InteractionHash
{

    private Interacts $interacts;

    public function __construct(Interacts $interacts)
    {
        $this->interacts = $interacts;
    }

    public function generate(): string
    {
        $segments = [
            get_class($this->interacts),
            get_class($this->interacts->getTarget()),
        ];

        if ($this->interacts instanceof RetrievesProperty) {
            $segments[] = $this->interacts->getProperty();
        }

        if ($this->interacts instanceof MutatesProperty) {
            $segments[] = $this->interacts->getValue();
        }

        if ($this->interacts instanceof InvokesMethod) {
            $segments[] = $this->interacts->getMethod();
            $segments[] = serialize($this->interacts->getParameters());
        }

        return md5(implode(':', $segments));
    }

}