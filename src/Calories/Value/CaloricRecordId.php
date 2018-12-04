<?php declare (strict_types=1);

namespace Caloriary\Calories\Value;

use Ramsey\Uuid\Exception\InvalidUuidStringException;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class CaloricRecordId
{
	/**
	 * @var UuidInterface
	 */
	private $uuid;


	/**
	 * @throws \InvalidArgumentException
	 */
	public static function fromString(string $uuid): self
	{
		$instance = new self;

		try {
			$instance->uuid = Uuid::fromString($uuid);
		} catch (InvalidUuidStringException $e) {
			throw new \InvalidArgumentException(sprintf(
				"Invalid uuid '%s' provided",
				$uuid
			));
		}

		return $instance;
	}


	private function __construct()
	{
	}
}
