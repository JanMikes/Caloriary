<?php declare (strict_types=1);

namespace Caloriary\Authorization;

use Caloriary\Authentication\User;

interface Resource
{
	public function ownedBy(): User;
}
