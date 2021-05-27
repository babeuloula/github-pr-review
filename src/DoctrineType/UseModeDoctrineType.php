<?php

/**
 * @author BaBeuloula <info@babeuloula.fr>
 */

declare(strict_types=1);

namespace App\DoctrineType;

use App\Enum\UseMode;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class UseModeDoctrineType extends Type
{
    public function getName(): string
    {
        return 'use_mode';
    }

    /** @param mixed[] $fieldDeclaration The field declaration. */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return $platform->getVarcharTypeDeclarationSQL($fieldDeclaration);
    }

    /** @param null|UseMode $value */
    // phpcs:ignore
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if (true === \is_null($value)) {
            return null;
        }

        return (string) $value;
    }

    /** @param null|string $value */
    // phpcs:ignore
    public function convertToPHPValue($value, AbstractPlatform $platform): ?UseMode
    {
        if (true === \is_null($value)) {
            return null;
        }

        return new UseMode($value);
    }

    public function getBindingType()
    {
        return \PDO::PARAM_STR;
    }

    // phpcs:ignore
    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }
}
