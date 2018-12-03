<?php declare (strict_types=1);

namespace Caloriary\Tests\Domain;

use Caloriary\Domain\Exception\AuthenticationFailed;
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


	/**
	 * @doesNotPerformAssertions
	 */
	public function testAuthenticate(): void
	{
		$user = User::register(
			EmailAddress::fromString('john@doe.com'),
			ClearTextPassword::fromString('password')
		);

		$user->authenticate(
			ClearTextPassword::fromString('password')
		);
	}


	public function testAuthenticateFails(): void
	{
		$this->expectException(AuthenticationFailed::class);

		$user = User::register(
			EmailAddress::fromString('john@doe.com'),
			ClearTextPassword::fromString('password')
		);

		$user->authenticate(
			ClearTextPassword::fromString('notMatchingPassword')
		);
	}
}
