<?php declare (strict_types=1);

namespace Caloriary\Tests\Domain\Value;

use Caloriary\Domain\Value\EmailAddress;
use PHPUnit\Framework\TestCase;

class EmailAddressTest extends TestCase
{

	public function testFromString(): void
	{
		$address = EmailAddress::fromString('john@doe.com');

		$this->assertInstanceOf(EmailAddress::class, $address);
	}


	/**
	 * @dataProvider fromStringShouldNotAcceptInvalidEmailAddressProvider
	 */
	public function testFromStringShouldNotAcceptInvalidEmailAddress(string $string): void
	{
		$this->expectException(\InvalidArgumentException::class);

		EmailAddress::fromString($string);
	}


	public function fromStringShouldNotAcceptInvalidEmailAddressProvider(): array
	{
		return [
			[''],
			['a@b'],
			['@b.c'],
		];
	}
}
