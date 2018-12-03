<?php declare (strict_types=1);

namespace Caloriary\Domain;

use Caloriary\Domain\Exception\AuthenticationFailed;
use Caloriary\Domain\Value\ClearTextPassword;
use Caloriary\Domain\Value\EmailAddress;
use Caloriary\Domain\Value\PasswordHash;

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


	public static function register(
		EmailAddress $emailAddress,
		ClearTextPassword $password
	): self
	{
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


	private function __construct(
		EmailAddress $emailAddress,
		PasswordHash $passwordHash
	)
	{
		$this->emailAddress = $emailAddress;
		$this->passwordHash = $passwordHash;
	}
}
