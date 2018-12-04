<?php declare (strict_types=1);

namespace Tests\Caloriary\Calories\Value;

use Caloriary\Calories\Value\DailyCaloriesLimit;
use PHPUnit\Framework\TestCase;

class DailyCaloriesLimitTest extends TestCase
{
	/**
	 * @doesNotPerformAssertions
	 */
	public function testFromInteger(): void
	{
		DailyCaloriesLimit::fromInteger(10);
	}


	public function testFromIntegerShouldNotAcceptLowerValueThanZero(): void
	{
		$this->expectException(\InvalidArgumentException::class);

		DailyCaloriesLimit::fromInteger(-1);
	}
}
