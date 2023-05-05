<?php

namespace App\Helpers;

use App\Helpers\Exception\EnvVarException;

/**
 * Class EnvVar
 */
class EnvVar
{
    /**
     * @param string $key - the name of the env var
     * @param mixed|null $fallbackValue - if null the function will throw an error if the $key is not found
     * @param bool $throw - if false, null will be returned instead of throwing EnvVarException
     * @return string - the value of the env var OR the $fallbackValue (if not null)
     * @throws EnvVarException
     */
    public static function get(string $key, $fallbackValue = null, bool $throw = true): string
    {
        if (!array_key_exists($key, $_ENV) && !array_key_exists($key, $_SERVER) && is_null($fallbackValue) && $throw) {
            throw new EnvVarException($key);
        }

        return $_ENV[$key] ?? $_SERVER[$key] ?? $fallbackValue;
    }
}