<?php
/**
 * @author BaBeuloula <info@babeuloula.fr>
 */
declare(strict_types=1);

namespace App\TypedArray;

use App\TypedArray\Type\Notification;
use steevanb\PhpTypedArray\ObjectArray\ObjectArray;

class NotificationArray extends ObjectArray
{
    public function __construct(array $values = [])
    {
        parent::__construct($values, Notification::class);
    }
}
