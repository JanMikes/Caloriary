<?php

declare(strict_types=1);

namespace Caloriary\Infrastructure\Authorization\DBAL\Type;

use Caloriary\Authorization\Value\UserRole;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

final class UserRoleType extends StringType
{
    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        \assert(is_string($value));

        return UserRole::get($value);
    }


    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        \assert($value instanceof UserRole);

        return parent::convertToDatabaseValue($value->getValue(), $platform);
    }


    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }


    public function getName(): string
    {
        return UserRole::class;
    }
}
