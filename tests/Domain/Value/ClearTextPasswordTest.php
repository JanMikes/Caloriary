<?php declare (strict_types=1);

namespace Caloriary\Tests\Domain\Value;

use Caloriary\Domain\Value\ClearTextPassword;
use PHPUnit\Framework\TestCase;

class ClearTextPasswordTest extends TestCase
{
	public function testFromStringShouldNotAcceptEmptyPassword(): void
	{
		$this->expectException(\InvalidArgumentException::class);

		ClearTextPassword::fromString('');
	}


	public function testMatches(): void
	{
		$string = '123';
		$password1 = ClearTextPassword::fromString($string);
		$password2 = ClearTextPassword::fromString($string);
		$hash1 = $password1->makeHash();
		$hash2 = $password2->makeHash();

		$this->assertTrue($password1->matches($hash1));
		$this->assertTrue($password1->matches($hash2));
		$this->assertTrue($password2->matches($hash1));
		$this->assertTrue($password2->matches($hash2));
	}


	public function testNotMatches(): void
	{
		$string1 = '123';
		$string2 = '321';
		$password1 = ClearTextPassword::fromString($string1);
		$password2 = ClearTextPassword::fromString($string2);
		$hash1 = $password1->makeHash();
		$hash2 = $password2->makeHash();

		$this->assertFalse($password1->matches($hash2));
		$this->assertFalse($password2->matches($hash1));
	}
}
