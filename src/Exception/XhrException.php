<?php

/**
 * @author BaBeuloula <info@babeuloula.fr>
 */

declare(strict_types=1);

namespace App\Exception;

class XhrException extends \Exception
{
    public function __construct()
    {
        parent::__construct('You must call this endpoint with XHR.');
    }
}
