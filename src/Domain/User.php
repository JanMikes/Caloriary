<?php declare (strict_types=1);

namespace Caloriary\Domain;

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


	private function __construct(
		EmailAddress $emailAddress,
		PasswordHash $passwordHash
	)
	{
		$this->emailAddress = $emailAddress;
		$this->passwordHash = $passwordHash;
	}
}
