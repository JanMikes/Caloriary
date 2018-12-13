<?php declare (strict_types=1);

namespace Caloriary\Infrastructure\Calories\DBAL\Type;

use Caloriary\Calories\Value\MealDescription;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\TextType;

final class MealDescriptionType extends TextType
{
	/**
	 * @inheritdoc
	 */
	public function convertToPHPValue($value, AbstractPlatform $platform)
	{
		\assert(is_string($value));

		return MealDescription::fromString($value);
	}


	/**
	 * @inheritdoc
	 */
	public function convertToDatabaseValue($value, AbstractPlatform $platform)
	{
		\assert($value instanceof MealDescription);

		return parent::convertToDatabaseValue($value->toString(), $platform);
	}


	public function requiresSQLCommentHint(AbstractPlatform $platform): bool
	{
		return true;
	}


	public function getName(): string
	{
		return MealDescription::class;
	}
}
