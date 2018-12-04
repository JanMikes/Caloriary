<?php declare (strict_types=1);

namespace Caloriary\Authentication\Repository;

use Caloriary\Authentication\User;
use Caloriary\Authentication\Value\EmailAddress;

interface Users
{
	public function get(EmailAddress $emailAddress): User;

	public function add(User $user): void;

	public function remove(User $user): void;
}
