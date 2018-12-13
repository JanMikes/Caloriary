<?php declare (strict_types=1);

namespace Caloriary\Infrastructure\Authentication\DBAL\Type;

use Caloriary\Authentication\Value\PasswordHash;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

final class PasswordHashType extends StringType
{
	/**
	 * @inheritdoc
	 */
	public function convertToPHPValue($value, AbstractPlatform $platform)
	{
		\assert(is_string($value));

		return PasswordHash::fromString($value);
	}


	/**
	 * @inheritdoc
	 */
	public function convertToDatabaseValue($value, AbstractPlatform $platform)
	{
		\assert($value instanceof PasswordHash);

		return parent::convertToDatabaseValue($value->toString(), $platform);
	}


	public function requiresSQLCommentHint(AbstractPlatform $platform): bool
	{
		return true;
	}


	public function getName(): string
	{
		return PasswordHash::class;
	}
}
