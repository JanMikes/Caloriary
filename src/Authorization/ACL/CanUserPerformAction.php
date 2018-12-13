<?php

declare(strict_types=1);

namespace Caloriary\Authorization\ACL;

use Caloriary\Authentication\User;
use Caloriary\Authorization\Value\UserAction;

interface CanUserPerformAction
{
    public function __invoke(User $user, UserAction $action): bool;
}
