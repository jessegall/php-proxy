<?php

namespace JesseGall\Proxy\Concerns;

use stdClass;

trait WrapsValues
{

    /**
     * Wrap value in given type.
     * If type is null, type will default static::class
     *
     * @param mixed $value
     * @param class-string<stdClass>|null $type
     * @return mixed
     */
    protected function wrapIfValueIsObject(mixed $value, string $type = null): mixed
    {
        if (! $type) {
            $type = static::class;
        }

        if (is_object($value)) {
            return new $type($value);
        }

        return $value;
    }

}