<?php

declare(strict_types=1);

namespace App\Exception;

class XhrException extends \Exception
{
    public function __construct(?\Throwable $previous = null)
    {
        parent::__construct('You must call this endpoint with XHR.', previous: $previous);
    }
}
