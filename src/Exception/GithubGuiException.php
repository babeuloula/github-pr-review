<?php

/**
 * @author BaBeuloula <info@babeuloula.fr>
 */

declare(strict_types=1);

namespace App\Exception;

class GithubGuiException extends \Exception
{
    public const MESSAGE_FILTERS_NOT_ENABLED = 'You need to use filters to access to this endpoint.';
    public const CODE_FILTERS_NOT_ENABLED = 1;
    public const CODE_FILTERS_NOT_ENABLED_XHR = 11;

    public const MESSAGE_FILTERS_ARE_EMPTY = 'Github\'s Filters cannot be empty.';
    public const CODE_FILTERS_ARE_EMPTY = 2;

    public const MESSAGE_CONFIG_IS_EMPTY = 'You configuration is not set. Please set it before use.';
    public const CODE_CONFIG_IS_EMPTY = 3;
    public const CODE_CONFIG_IS_EMPTY_XHR = 33;
}
