<?php declare (strict_types=1);

namespace Tests\Caloriary\Calories\Value;

use Caloriary\Calories\Value\MealDescription;
use PHPUnit\Framework\TestCase;

class MealDescriptionTest extends TestCase
{
	public function testFromStringShouldNotAcceptEmptyPassword(): void
	{
		$this->expectException(\InvalidArgumentException::class);

		MealDescription::fromString('');
	}
}
