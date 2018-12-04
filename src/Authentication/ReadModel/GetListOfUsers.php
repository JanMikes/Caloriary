<?php declare (strict_types=1);

namespace Caloriary\Authentication\ReadModel;

use Caloriary\Authentication\User;

interface GetListOfUsers
{
	/**
	 * @todo: filtering
	 * @todo: paging
	 *
	 * @return User[]
	 */
	public function __invoke(): array;
}
