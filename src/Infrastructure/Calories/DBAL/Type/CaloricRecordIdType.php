<?php declare (strict_types=1);

namespace Caloriary\Infrastructure\Calories\DBAL\Type;

use Caloriary\Calories\Value\CaloricRecordId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

final class CaloricRecordIdType extends StringType
{
	public function convertToPHPValue($value, AbstractPlatform $platform)
	{
		\assert(is_string($value));

		return CaloricRecordId::fromString($value);
	}


	public function convertToDatabaseValue($value, AbstractPlatform $platform)
	{
		\assert($value instanceof CaloricRecordId);

		return parent::convertToDatabaseValue($value->toString(), $platform);
	}


	public function requiresSQLCommentHint(AbstractPlatform $platform): bool
	{
		return true;
	}


	public function getName(): string
	{
		return CaloricRecordId::class;
	}
}
