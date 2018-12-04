<?php declare (strict_types=1);

namespace Caloriary\Authorization\ReadModel;

use Caloriary\Authentication\User;
use Caloriary\Authorization\Resource;

interface IsUserOwnerOfResource
{
	public function __invoke(User $user, Resource $resource): bool;
}
