<?php declare (strict_types=1);

namespace Tests\Caloriary\Authentication;

use Caloriary\Authentication\Exception\AuthenticationFailed;
use Caloriary\Authentication\Exception\EmailAddressAlreadyRegistered;
use Caloriary\Authentication\ReadModel\IsEmailRegistered;
use Caloriary\Authentication\User;
use Caloriary\Authentication\Value\ClearTextPassword;
use Caloriary\Authentication\Value\EmailAddress;
use Caloriary\Authorization\Exception\RestrictedAccess;
use Caloriary\Authorization\ReadModel\CanUserPerformActionOnResource;
use Caloriary\Authorization\Value\UserRole;
use League\FactoryMuffin\FactoryMuffin;
use PHPUnit\Framework\TestCase;
use Tests\Caloriary\AuthorizationMockFactoryMethods;

class UserTest extends TestCase
{
	use AuthorizationMockFactoryMethods;

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

		$clearTextPassword = \Mockery::mock(ClearTextPassword::class);
		$clearTextPassword->shouldIgnoreMissing();

		$user = User::register(
			\Mockery::mock(EmailAddress::class),
			$clearTextPassword,
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


	public function testAuthenticateFails(): void
	{
		$this->expectException(AuthenticationFailed::class);

		/** @var User $user */
		$user = static::$fm->instance(User::class);

		$user->authenticate(
			ClearTextPassword::fromString('notMatchingPassword')
		);
	}


	public function testEditUser(): void
	{
		/** @var User $user1 */
		$user1 = static::$fm->instance(User::class);
		/** @var User $user2 */
		$user2 = static::$fm->instance(User::class);

		$canPerformActionOnResource = $this->createCanPerformActionOnResourceMock(true);

		$user1->editUser(
			$user2,
			10,
			$canPerformActionOnResource
		);

		$this->assertSame(10, $user2->dailyLimit());
	}


	public function testEditUserShouldThrowExceptionWhenUnauthorized(): void
	{
		$this->expectException(RestrictedAccess::class);

		/** @var User $user1 */
		$user1 = static::$fm->instance(User::class);
		/** @var User $user2 */
		$user2 = static::$fm->instance(User::class);

		$canPerformActionOnResource = $this->createCanPerformActionOnResourceMock(false);

		$user1->editUser(
			$user2,
			10,
			$canPerformActionOnResource
		);
	}


	/**
	 * @doesNotPerformAssertions
	 */
	public function testChangeUserPassword(): void
	{
		/** @var User $user1 */
		$user1 = static::$fm->instance(User::class);
		/** @var User $user2 */
		$user2 = static::$fm->instance(User::class);

		$canPerformActionOnResource = $this->createCanPerformActionOnResourceMock(true);

		$user1->changeUserPassword(
			$user2,
			ClearTextPassword::fromString('abcd'),
			$canPerformActionOnResource
		);

		$user2->authenticate(
			ClearTextPassword::fromString('abcd')
		);
	}


	public function testChangeUserPasswordShouldThrowExceptionWhenUnauthorized(): void
	{
		$this->expectException(RestrictedAccess::class);

		/** @var User $user1 */
		$user1 = static::$fm->instance(User::class);
		/** @var User $user2 */
		$user2 = static::$fm->instance(User::class);

		$canPerformActionOnResource = $this->createCanPerformActionOnResourceMock(false);

		$user1->changeUserPassword(
			$user2,
			ClearTextPassword::fromString('abcd'),
			$canPerformActionOnResource
		);
	}


	/**
	 * @doesNotPerformAssertions
	 */
	public function testChangeUserRole(): void
	{
		/** @var User $user1 */
		$user1 = static::$fm->instance(User::class);
		/** @var User $user2 */
		$user2 = static::$fm->instance(User::class);

		$canPerformActionOnResource = $this->createCanPerformActionOnResourceMock(true);

		$user1->changeUserRole(
			$user2,
			UserRole::get(UserRole::ADMIN),
			$canPerformActionOnResource
		);
	}


	public function testChangeUserRoleShouldThrowExceptionWhenUnauthorized(): void
	{
		$this->expectException(RestrictedAccess::class);

		/** @var User $user1 */
		$user1 = static::$fm->instance(User::class);
		/** @var User $user2 */
		$user2 = static::$fm->instance(User::class);

		$canPerformActionOnResource = $this->createCanPerformActionOnResourceMock(false);

		$user1->changeUserRole(
			$user2,
			UserRole::get(UserRole::ADMIN),
			$canPerformActionOnResource
		);
	}
}
