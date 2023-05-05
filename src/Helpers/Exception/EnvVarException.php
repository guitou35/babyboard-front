<?php

namespace App\Helpers\Exception;

use Error;

/**
 * Class EnvVarException
 */
class EnvVarException extends Error
{
    /**
     * EnvVarException constructor.
     * @param string $key
     */
    public function __construct(string $key)
    {
        parent::__construct("The environment variable '$key' is not set.");
    }
}