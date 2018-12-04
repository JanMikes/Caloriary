<?php declare (strict_types=1);

namespace Tests\Caloriary\Calories\Value;

use Caloriary\Calories\Value\CaloricRecordId;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class CaloricRecordIdTest extends TestCase
{
	/**
	 * @doesNotPerformAssertions
	 */
	public function testFromString(): void
	{
		CaloricRecordId::fromString(Uuid::uuid4()->toString());
	}


	/**
	 * @dataProvider fromStringShouldNotAcceptInvalidUuidString
	 */
	public function testFromStringShouldNotAcceptInvalidUuidString(string $uuid): void
	{
		$this->expectException(\InvalidArgumentException::class);

		CaloricRecordId::fromString($uuid);
	}


	public function fromStringShouldNotAcceptInvalidUuidString(): array
	{
		return [
			[''],
			['abcd']
		];
	}
}
