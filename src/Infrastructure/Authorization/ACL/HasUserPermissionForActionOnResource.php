<?php

declare(strict_types=1);

namespace Caloriary\Infrastructure\Authorization\ACL;

use Caloriary\Authentication\User;
use Caloriary\Authorization\ACL\CanUserPerformActionOnResource;
use Caloriary\Authorization\Resource;
use Caloriary\Authorization\Value\UserAction;

final class HasUserPermissionForActionOnResource implements CanUserPerformActionOnResource
{
    /**
     * @var HasUserPermissionForAction
     */
    private $hasUserPermissionOnAction;


    public function __construct(HasUserPermissionForAction $hasUserPermissionOnAction)
    {
        $this->hasUserPermissionOnAction = $hasUserPermissionOnAction;
    }


    public function __invoke(User $user, UserAction $action, Resource $resource): bool
    {
        if ($resource->ownedBy()->emailAddress()->toString() === $user->emailAddress()->toString()) {
            return true;
        }

        return $this->hasUserPermissionOnAction->__invoke($user, $action);
    }
}
