<?php

declare(strict_types=1);

namespace App\Exception;

class FiltersNotEnabledException extends \Exception
{
    public function __construct(?\Throwable $previous = null)
    {
        parent::__construct(
            'You need to use filters to access to this endpoint.',
            1,
            $previous
        );
    }
}
