<?php declare (strict_types=1);

namespace Caloriary\Tests\Domain;

use Caloriary\Domain\User;
use Caloriary\Domain\Value\ClearTextPassword;
use Caloriary\Domain\Value\EmailAddress;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
	public function testRegister(): void
	{
		$user = User::register(
			EmailAddress::fromString('john@doe.com'),
			ClearTextPassword::fromString('password')
		);

		$this->assertInstanceOf(User::class, $user);
	}
}
