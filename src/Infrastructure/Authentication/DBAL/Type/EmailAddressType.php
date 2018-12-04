<?php declare (strict_types=1);

namespace Caloriary\Infrastructure\Authentication\DBAL\Type;

use Caloriary\Authentication\Value\EmailAddress;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

final class EmailAddressType extends StringType
{
	public function convertToPHPValue($value, AbstractPlatform $platform)
	{
		\assert(is_string($value));

		return EmailAddress::fromString($value);
	}


	public function convertToDatabaseValue($value, AbstractPlatform $platform)
	{
		\assert($value instanceof EmailAddress);

		return parent::convertToDatabaseValue($value->toString(), $platform);
	}


	public function requiresSQLCommentHint(AbstractPlatform $platform): bool
	{
		return true;
	}


	public function getName(): string
	{
		return EmailAddress::class;
	}
}
