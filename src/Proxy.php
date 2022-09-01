<?php

namespace JesseGall\Proxy;

use JesseGall\Proxy\Concerns\ForwardsInteractions;
use JesseGall\Proxy\Concerns\WrapsValues;

/**
 * @template T
 * @mixin T
 */
class Proxy
{
    use ForwardsInteractions, WrapsValues;

    /**
     * @var T
     */
    protected object $subject;

    /**
     * T $subject
     */
    public function __construct(object $subject)
    {
        $this->subject = $subject;
    }

    /**
     * Forward the method call to the subject,
     * Wrap the result in a proxy if the result is an object.
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call(string $method, array $parameters): mixed
    {
        return $this->wrapIfValueIsObject(
            $this->forwardCallTo($this->subject, $method, $parameters)
        );
    }

    /**
     * Forward the property accessor to the subject.
     * Wraps the result in a proxy if the result is an object.
     *
     * @param string $property
     * @return mixed
     */
    public function __get(string $property): mixed
    {
        return $this->wrapIfValueIsObject(
            $this->forwardGetTo($this->subject, $property)
        );
    }

}