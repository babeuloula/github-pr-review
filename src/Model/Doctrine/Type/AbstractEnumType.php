<?php

declare(strict_types=1);

namespace App\Model\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

abstract class AbstractEnumType extends Type
{
    /** @return class-string<\BackedEnum> */
    abstract public static function getEnumClass(): string;

    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        if (false === \array_key_exists('type', $column)) {
            throw new \InvalidArgumentException('Wrong enum column type.');
        }

        $values = array_map(
            static function ($enumCase) {
                return "'" . $enumCase->value . "'";
            },
            static::getEnumClass()::cases()
        );

        return "ENUM(" . implode(", ", $values) . ")";
    }

    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if (true === $value instanceof \BackedEnum) {
            return (string) $value->value;
        }

        return null;
    }

    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
    public function convertToPHPValue($value, AbstractPlatform $platform): ?\BackedEnum
    {
        if (false === \enum_exists($this->getEnumClass(), true)) {
            throw new \LogicException("This class should be an enum.");
        }

        if (false === \is_string($value)) {
            throw new \LogicException("Value must be a string.");
        }

        return $this::getEnumClass()::tryFrom($value);
    }

    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
