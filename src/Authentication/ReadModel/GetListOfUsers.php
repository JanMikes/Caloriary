<?php declare (strict_types=1);

namespace Caloriary\Authentication\ReadModel;

use Caloriary\Authentication\User;

interface GetListOfUsers
{
	/**
	 * @return User[]
	 */
	public function __invoke(): array;
}
