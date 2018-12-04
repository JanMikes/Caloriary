<?php declare (strict_types=1);

namespace Caloriary\Authentication;

use Caloriary\Authentication\Exception\AuthenticationFailed;
use Caloriary\Authentication\Exception\EmailAddressAlreadyRegistered;
use Caloriary\Authentication\ReadModel\IsEmailRegistered;
use Caloriary\Authentication\Value\ClearTextPassword;
use Caloriary\Authentication\Value\EmailAddress;
use Caloriary\Authentication\Value\PasswordHash;
use Caloriary\Authorization\ReadModel\CanUserPerformActionOnResource;
use Caloriary\Authorization\Exception\RestrictedAccess;
use Caloriary\Authorization\Resource;
use Caloriary\Authorization\Value\UserAction;
use Caloriary\Authorization\Value\UserRole;

final class User implements Resource
{
	/**
	 * @var EmailAddress
	 */
	private $emailAddress;

	/**
	 * @var PasswordHash
	 */
	private $passwordHash;

	/**
	 * @var UserRole
	 */
	private $role;

	/**
	 * @var int
	 */
	private $dailyLimit = 0;


	public static function register(
		EmailAddress $emailAddress,
		ClearTextPassword $password,
		IsEmailRegistered $isEmailRegistered
	): self
	{
		if ($isEmailRegistered->__invoke($emailAddress)) {
			throw new EmailAddressAlreadyRegistered();
		}

		return new self($emailAddress, $password->makeHash());
	}


	public function emailAddress(): EmailAddress
	{
		return $this->emailAddress;
	}


	public function passwordHash(): PasswordHash
	{
		return $this->passwordHash;
	}


	public function authenticate(ClearTextPassword $password) : void
	{
		if ($password->matches($this->passwordHash) === false) {
			throw new AuthenticationFailed();
		}
	}


	public function editUser(
		User $user,
		int $dailyLimit,
		CanUserPerformActionOnResource $canUserPerformActionOnResource
	): void
	{
		$action = UserAction::get(UserAction::EDIT_USER);

		$this->assertActionCanBePerformedOnResource($canUserPerformActionOnResource, $action, $user);

		$user->dailyLimit = $dailyLimit;
	}


	public function changeUserPassword(
		User $user,
		ClearTextPassword $password,
		CanUserPerformActionOnResource $canUserPerformActionOnResource
	): void
	{
		$action = UserAction::get(UserAction::CHANGE_USER_PASSWORD);

		$this->assertActionCanBePerformedOnResource($canUserPerformActionOnResource, $action, $user);

		$user->passwordHash = $password->makeHash();
	}


	public function changeUserRole(
		User $user,
		UserRole $role,
		CanUserPerformActionOnResource $canUserPerformActionOnResource
	): void
	{
		$action = UserAction::get(UserAction::CHANGE_USER_ROLE);

		$this->assertActionCanBePerformedOnResource($canUserPerformActionOnResource, $action, $user);

		$this->role = $role;
	}


	public function ownedBy(): User
	{
		return $this;
	}


	private function __construct(
		EmailAddress $emailAddress,
		PasswordHash $passwordHash
	)
	{
		$this->emailAddress = $emailAddress;
		$this->passwordHash = $passwordHash;
		$this->role = UserRole::get(UserRole::USER);
	}


	private function assertActionCanBePerformedOnResource(
		CanUserPerformActionOnResource $canUserPerformActionOnResource,
		UserAction $action,
		Resource $resource
	): void
	{
		if (! $canUserPerformActionOnResource->__invoke($this, $action, $resource)) {
			throw new RestrictedAccess();
		}
	}
}
