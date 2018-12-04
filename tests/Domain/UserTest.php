<?php declare (strict_types=1);

namespace Caloriary\Tests\Domain;

use Caloriary\Domain\Exception\AuthenticationFailed;
use Caloriary\Domain\Exception\EmailAddressAlreadyRegistered;
use Caloriary\Domain\ReadModel\IsEmailRegistered;
use Caloriary\Domain\User;
use Caloriary\Domain\Value\ClearTextPassword;
use Caloriary\Domain\Value\EmailAddress;
use League\FactoryMuffin\FactoryMuffin;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
	/**
	 * @var FactoryMuffin
	 */
	protected static $fm;


	public static function setUpBeforeClass(): void
	{
		parent::setUpBeforeClass();

		static::$fm = new FactoryMuffin();
		static::$fm->loadFactories(__DIR__ . '/../factories');
	}


	/**
	 * @dataProvider registerProvider
	 */
	public function testRegister(bool $isRegistered): void
	{
		if ($isRegistered) {
			$this->expectException(EmailAddressAlreadyRegistered::class);
		}

		$isEmailRegistered = \Mockery::mock(IsEmailRegistered::class);
		$isEmailRegistered->shouldReceive('__invoke')->andReturn($isRegistered);

		$user = User::register(
			EmailAddress::fromString('john@doe.com'),
			ClearTextPassword::fromString('password'),
			$isEmailRegistered
		);

		$this->assertInstanceOf(User::class, $user);
	}


	public function registerProvider(): array
	{
		return [
			[true],
			[false],
		];
	}


	/**
	 * @doesNotPerformAssertions
	 */
	public function testAuthenticate(): void
	{
		$user = static::$fm->instance(User::class);

		$user->authenticate(
			ClearTextPassword::fromString('123')
		);
	}


	public function testAuthenticateFails(): void
	{
		$this->expectException(AuthenticationFailed::class);

		$user = static::$fm->instance(User::class);

		$user->authenticate(
			ClearTextPassword::fromString('notMatchingPassword')
		);
	}
}
