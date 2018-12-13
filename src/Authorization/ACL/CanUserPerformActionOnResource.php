<?php

declare(strict_types=1);

namespace Caloriary\Authorization\ACL;

use Caloriary\Authentication\User;
use Caloriary\Authorization\Resource;
use Caloriary\Authorization\Value\UserAction;

interface CanUserPerformActionOnResource
{
    public function __invoke(User $user, UserAction $action, Resource $resource): bool;
}
