<?php

declare(strict_types=1);

namespace App\Exception;

class EmptyFilterException extends \Exception
{
    public function __construct(?\Throwable $previous = null)
    {
        parent::__construct(
            'Github\'s Filters cannot be empty.',
            2,
            $previous
        );
    }
}
