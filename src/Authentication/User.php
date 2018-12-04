<?php declare (strict_types=1);

namespace Caloriary\Authentication;

use Caloriary\Authentication\Exception\AuthenticationFailed;
use Caloriary\Authentication\Exception\EmailAddressAlreadyRegistered;
use Caloriary\Authentication\ReadModel\IsEmailRegistered;
use Caloriary\Authentication\Value\ClearTextPassword;
use Caloriary\Authentication\Value\EmailAddress;
use Caloriary\Authentication\Value\PasswordHash;
use Caloriary\Authorization\Value\UserRole;

final class User
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
	 * @var \Caloriary\Authorization\Value\UserRole
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


	public function editByUser(User $user, int $dailyLimit): void
	{
		// @todo: Check if user is allowed to edit this user
		$this->dailyLimit = $dailyLimit;
	}


	public function changePasswordByUser(User $user, ClearTextPassword $password): void
	{
		// @todo: Check if user is allowed to perform action
		$this->passwordHash = $password->makeHash();
	}


	public function changeRoleUser(User $user, UserRole $role): void
	{
		// @todo: Check if user is allowed to perform action
		$this->role = $role;
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
}
