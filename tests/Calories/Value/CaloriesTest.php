<?php declare (strict_types=1);

namespace Tests\Caloriary\Calories\Value;

use Caloriary\Calories\Value\Calories;
use PHPUnit\Framework\TestCase;

class CaloriesTest extends TestCase
{
	/**
	 * @doesNotPerformAssertions
	 */
	public function testFromInteger(): void
	{
		Calories::fromInteger(1);
	}


	/**
	 * @dataProvider fromIntegerShouldNotAcceptLessOrEqualToZeroProvider
	 */
	public function testFromIntegerShouldNotAcceptLessOrEqualToZero(int $calories): void
	{
		$this->expectException(\InvalidArgumentException::class);

		Calories::fromInteger($calories);
	}


	public function fromIntegerShouldNotAcceptLessOrEqualToZeroProvider()
	{
		return [
			[0],
			[-1]
		];
	}
}
