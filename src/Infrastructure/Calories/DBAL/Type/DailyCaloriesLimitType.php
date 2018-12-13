<?php

declare(strict_types=1);

namespace Caloriary\Infrastructure\Calories\DBAL\Type;

use Caloriary\Calories\Value\DailyCaloriesLimit;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\IntegerType;

final class DailyCaloriesLimitType extends IntegerType
{
    /**
     * @inheritdoc
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        \assert(is_numeric($value));

        $value = (int) $value;

        return DailyCaloriesLimit::fromInteger($value);
    }


    /**
     * @inheritdoc
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        \assert($value instanceof DailyCaloriesLimit);

        return parent::convertToDatabaseValue($value->toInteger(), $platform);
    }


    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }


    public function getName(): string
    {
        return DailyCaloriesLimit::class;
    }
}
