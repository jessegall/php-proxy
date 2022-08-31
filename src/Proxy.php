<?php

namespace JesseGall\Proxy;

/**
 * @template T
 * @mixin T
 */
class Proxy
{

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
        return $this->wrapIfIsObject($this->subject->{$method}(...$parameters));
    }

    /**
     * Forward the property accessor to the subject.
     * Wraps the result in a proxy if the result is an object.
     *
     * @param string $name
     * @return mixed
     */
    public function __get(string $name): mixed
    {
        return $this->wrapIfIsObject($this->subject->{$name});
    }

    /**
     * Wrap value in a proxy when value is an object
     *
     * @param mixed $value
     * @return mixed
     */
    protected function wrapIfIsObject(mixed $value): mixed
    {
        if (is_object($value)) {
            return new static($value);
        }

        return $value;
    }

}