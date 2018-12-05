<?php declare (strict_types=1);

namespace Caloriary\Infrastructure\Calories\DBAL\Type;

use Caloriary\Calories\Value\Calories;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\IntegerType;

final class CaloriesType extends IntegerType
{
	public function convertToPHPValue($value, AbstractPlatform $platform)
	{
		\assert(is_numeric($value));

		$value = (int) $value;

		return Calories::fromInteger($value);
	}


	public function convertToDatabaseValue($value, AbstractPlatform $platform)
	{
		\assert($value instanceof Calories);

		return parent::convertToDatabaseValue($value->toInteger(), $platform);
	}


	public function requiresSQLCommentHint(AbstractPlatform $platform): bool
	{
		return true;
	}


	public function getName(): string
	{
		return Calories::class;
	}
}
