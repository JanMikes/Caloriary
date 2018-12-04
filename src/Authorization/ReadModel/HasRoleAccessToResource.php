<?php declare (strict_types=1);

namespace Caloriary\Authorization\ReadModel;

use Caloriary\Authorization\Value\UserAction;
use Caloriary\Authorization\Value\UserRole;

interface HasRoleAccessToResource
{
	public function __invoke(UserRole $role, UserAction $resource): bool;
}
