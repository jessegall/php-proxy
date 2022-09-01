<?php

namespace JesseGall\Proxy\Concerns;

trait ForwardsInteractions
{

    /**
     * Forward the call to the given subject
     *
     * @param object $subject
     * @param string $method
     * @param $parameters
     * @return mixed
     */
    protected function forwardCallTo(object $subject, string $method, $parameters): mixed
    {
        return $subject->{$method}(...$parameters);
    }

    /**
     * Forward the accessor to the given subject
     *
     * @param object $subject
     * @param string $property
     * @return mixed
     */
    protected function forwardGetTo(object $subject, string $property): mixed
    {
        return $subject->{$property};
    }

}