<?php

declare(strict_types=1);

namespace App\Doctrine\Type;

use App\Enum\Color;
use App\Model\Doctrine\Type\AbstractEnumType;

final class EnumColor extends AbstractEnumType
{
    public const NAME = 'enum_color';

    public function getName(): string
    {
        return self::NAME;
    }

    public static function getEnumClass(): string
    {
        return Color::class;
    }
}
