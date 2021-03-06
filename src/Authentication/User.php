<?php

declare(strict_types=1);

namespace Caloriary\Authentication;

use Caloriary\Authentication\Exception\AuthenticationFailed;
use Caloriary\Authentication\Exception\EmailAddressAlreadyRegistered;
use Caloriary\Authentication\ReadModel\IsEmailRegistered;
use Caloriary\Authentication\Value\ClearTextPassword;
use Caloriary\Authentication\Value\EmailAddress;
use Caloriary\Authentication\Value\PasswordHash;
use Caloriary\Authorization\ACL\CanUserPerformAction;
use Caloriary\Authorization\ACL\CanUserPerformActionOnResource;
use Caloriary\Authorization\Exception\RestrictedAccess;
use Caloriary\Authorization\Resource;
use Caloriary\Authorization\Value\UserAction;
use Caloriary\Authorization\Value\UserRole;
use Caloriary\Calories\Value\DailyCaloriesLimit;

class User implements Resource
{
    /**
     * @var EmailAddress
     */
    private $emailAddress;

    /**
     * @var PasswordHash
     */
    private $passwordHash;

    /**
     * @var UserRole
     */
    private $role;

    /**
     * @var DailyCaloriesLimit
     */
    private $dailyLimit;


    public static function register(
        EmailAddress $emailAddress,
        ClearTextPassword $password,
        IsEmailRegistered $isEmailRegistered
    ): self {
        if ($isEmailRegistered->__invoke($emailAddress)) {
            throw new EmailAddressAlreadyRegistered($emailAddress);
        }

        return new self($emailAddress, $password->makeHash());
    }


    public static function createByAdmin(
        EmailAddress $emailAddress,
        ClearTextPassword $password,
        DailyCaloriesLimit $dailyLimit,
        User $editor,
        IsEmailRegistered $isEmailRegistered,
        CanUserPerformAction $canUserPerformAction
    ): self {
        $action = UserAction::get(UserAction::ADD_USER);

        if (!$canUserPerformAction->__invoke($editor, $action)) {
            throw new RestrictedAccess();
        }

        if ($isEmailRegistered->__invoke($emailAddress)) {
            throw new EmailAddressAlreadyRegistered($emailAddress);
        }

        $user = new self($emailAddress, $password->makeHash());

        $user->dailyLimit = $dailyLimit;

        return $user;
    }


    public function emailAddress(): EmailAddress
    {
        return $this->emailAddress;
    }


    public function passwordHash(): PasswordHash
    {
        return $this->passwordHash;
    }


    /**
     * @throws AuthenticationFailed
     */
    public function authenticate(ClearTextPassword $password): void
    {
        if ($password->matches($this->passwordHash) === false) {
            throw new AuthenticationFailed();
        }
    }


    /**
     * @throws RestrictedAccess
     */
    public function editUser(
        User $user,
        DailyCaloriesLimit $dailyLimit,
        CanUserPerformActionOnResource $canUserPerformActionOnResource
    ): void {
        $action = UserAction::get(UserAction::EDIT_USER);

        $this->assertActionCanBePerformedOnResource($canUserPerformActionOnResource, $action, $user);

        $user->dailyLimit = $dailyLimit;
    }


    /**
     * @throws RestrictedAccess
     */
    public function changeUserPassword(
        User $user,
        ClearTextPassword $password,
        CanUserPerformActionOnResource $canUserPerformActionOnResource
    ): void {
        $action = UserAction::get(UserAction::CHANGE_USER_PASSWORD);

        $this->assertActionCanBePerformedOnResource($canUserPerformActionOnResource, $action, $user);

        $user->passwordHash = $password->makeHash();
    }


    /**
     * @throws RestrictedAccess
     */
    public function changeUserRole(
        User $user,
        UserRole $role,
        CanUserPerformActionOnResource $canUserPerformActionOnResource
    ): void {
        $action = UserAction::get(UserAction::CHANGE_USER_ROLE);

        $this->assertActionCanBePerformedOnResource($canUserPerformActionOnResource, $action, $user);

        $this->role = $role;
    }


    public function ownedBy(): User
    {
        return $this;
    }


    public function dailyLimit(): DailyCaloriesLimit
    {
        return $this->dailyLimit;
    }


    public function role(): UserRole
    {
        return $this->role;
    }


    private function __construct(
        EmailAddress $emailAddress,
        PasswordHash $passwordHash
    ) {
        $this->emailAddress = $emailAddress;
        $this->passwordHash = $passwordHash;
        $this->role = UserRole::get(UserRole::USER);
        $this->dailyLimit = DailyCaloriesLimit::createUnlimited();
    }


    /**
     * @throws RestrictedAccess
     */
    private function assertActionCanBePerformedOnResource(
        CanUserPerformActionOnResource $canUserPerformActionOnResource,
        UserAction $action,
        Resource $resource
    ): void {
        if (!$canUserPerformActionOnResource->__invoke($this, $action, $resource)) {
            throw new RestrictedAccess();
        }
    }
}
