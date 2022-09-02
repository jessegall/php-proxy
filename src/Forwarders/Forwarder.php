<?php

namespace JesseGall\Proxy\Forwarders;

use Closure;
use JesseGall\Proxy\Interceptors\Interceptor;

abstract class Forwarder
{

    private Interceptor $interceptor;

    public function __construct(Closure $interceptor = null)
    {
        $this->interceptor = new Interceptor($interceptor);
    }

    public function __invoke(object $target, ...$args): mixed
    {
        if ($result = ($this->interceptor)($target, ...$args)) {
            return $result;
        };

        return $this->forward($target, ...$args);
    }

    /**
     * @return Interceptor
     */
    public function getInterceptor(): Interceptor
    {
        return $this->interceptor;
    }

    /**
     * @param Interceptor|Closure $interceptor
     * @return Forwarder
     */
    public function setInterceptor(Interceptor|Closure $interceptor): static
    {
        if ($interceptor instanceof Interceptor) {
            $this->interceptor = $interceptor;
        } else {
            $this->interceptor->setHandler($interceptor);
        }

        return $this;
    }

    /**
     * @param object $target
     * @param ...$args
     * @return mixed
     */
    protected abstract function forward(object $target, ...$args): mixed;


}