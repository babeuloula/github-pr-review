<?php
/**
 * @author BaBeuloula <info@babeuloula.fr>
 */
declare(strict_types=1);

namespace App\TypedArray;

use App\TypedArray\Type\PullRequest;
use steevanb\PhpTypedArray\ObjectArray\ObjectArray;

class PullRequestArray extends ObjectArray
{
    public function __construct(array $values = [])
    {
        parent::__construct($values, PullRequest::class);
    }
}
