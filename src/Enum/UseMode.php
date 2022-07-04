<?php

declare(strict_types=1);

namespace App\Enum;

enum UseMode: string
{
    case FILTER = 'filter';
    case LABEL = 'label';

    public static function default(): self
    {
        return self::LABEL;
    }

    public function isFilter(): bool
    {
        return $this->value === self::FILTER->value;
    }

    public function isLabel(): bool
    {
        return $this->value === self::LABEL->value;
    }
}
