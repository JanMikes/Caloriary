<?php declare (strict_types=1);

namespace Tests\Caloriary\Authentication\Value;

use Caloriary\Authentication\Value\PasswordHash;
use PHPUnit\Framework\TestCase;

class PasswordHashTest extends TestCase
{
	/**
	 * @dataProvider fromStringShouldNotAcceptInvalidHashProvider
	 */
	public function testFromStringShouldNotAcceptInvalidHash(string $string): void
	{
		$this->expectException(\InvalidArgumentException::class);

		PasswordHash::fromString($string);
	}


	public function fromStringShouldNotAcceptInvalidHashProvider(): array
	{
		return [
			[''],
			['string'],
		];
	}
}
