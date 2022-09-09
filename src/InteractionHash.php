<?php

namespace JesseGall\Proxy;

use JesseGall\Proxy\Interactions\Contracts\Interacts;
use JesseGall\Proxy\Interactions\Contracts\InteractsWithAndModifiesProperty;
use JesseGall\Proxy\Interactions\Contracts\InteractsWithMethod;
use JesseGall\Proxy\Interactions\Contracts\InteractsWithProperty;

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

        if ($this->interacts instanceof InteractsWithProperty) {
            $segments[] = $this->interacts->getProperty();
        }

        if ($this->interacts instanceof InteractsWithAndModifiesProperty) {
            $segments[] = $this->interacts->getValue();
        }

        if ($this->interacts instanceof InteractsWithMethod) {
            $segments[] = $this->interacts->getMethod();
            $segments[] = serialize($this->interacts->getParameters());
        }

        return md5(implode(':', $segments));
    }

    public function __toString(): string
    {
        return $this->generate();
    }

}