<?php declare (strict_types=1);

namespace Caloriary\Infrastructure\Authentication;

use Caloriary\Authentication\Repository\Users;
use Caloriary\Authentication\User;
use Caloriary\Authentication\Value\EmailAddress;
use Caloriary\Authorization\Exception\RestrictedAccess;

final class UserProvider
{
	/**
	 * @var Users
	 */
	private $users;

	/**
	 * @var User|null
	 */
	private $currentUser;


	public function __construct(Users $users)
	{
		$this->users = $users;
	}


	public function populateUser(EmailAddress $emailAddress): void
	{
		$this->currentUser = $this->users->get($emailAddress);
	}


	public function currentUser(): User
	{
		if (!$this->currentUser) {
			throw new RestrictedAccess('User is not logged in');
		}

		return $this->currentUser;
	}
}
