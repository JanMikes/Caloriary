<?php declare (strict_types=1);

namespace Caloriary\Infrastructure\Authentication\ReadModel;

use Caloriary\Authentication\Exception\UserNotFound;
use Caloriary\Authentication\ReadModel\IsEmailRegistered;
use Caloriary\Authentication\Repository\Users;
use Caloriary\Authentication\Value\EmailAddress;

final class DoesEmailExistInRepository implements IsEmailRegistered
{
	/**
	 * @var Users
	 */
	private $users;


	public function __construct(Users $users)
	{
		$this->users = $users;
	}


	public function __invoke(EmailAddress $emailAddress): bool
	{
		try {
			$this->users->get($emailAddress);

			return true;
		} catch (UserNotFound $e) {
			return false;
		}
	}
}
