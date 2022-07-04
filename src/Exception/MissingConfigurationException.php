<?php

declare(strict_types=1);

namespace App\Exception;

class MissingConfigurationException extends \Exception
{
    public function __construct(?\Throwable $previous = null)
    {
        parent::__construct(
            'You configuration is not set. Please set it before use.',
            3,
            $previous
        );
    }
}
