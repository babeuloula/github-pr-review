<?php

declare(strict_types=1);

namespace App\Doctrine\Type;

use App\Enum\UseMode;
use App\Model\Doctrine\Type\AbstractEnumType;

final class EnumUseMode extends AbstractEnumType
{
    public const NAME = 'enum_use_mode';

    public function getName(): string
    {
        return self::NAME;
    }

    public static function getEnumClass(): string
    {
        return UseMode::class;
    }
}
