<?php declare (strict_types=1);

namespace Caloriary\Infrastructure\Authorization\ACL;

use Caloriary\Authentication\User;
use Caloriary\Authorization\ACL\CanUserPerformAction;
use Caloriary\Authorization\Value\UserAction;
use Nette\Security\Permission;

final class Permissions implements CanUserPerformAction
{
	/**
	 * @var Permission
	 */
	private $permission;


	public function __construct(array $permittedActionsForUsers)
	{
		$this->permission = new Permission();

		foreach ($permittedActionsForUsers as $role => $actions) {
			foreach ($actions as $action) {
				$this->allowActionForRole($action, $role);
			}
		}
	}


	public function __invoke(User $user, UserAction $action): bool
	{
		return $this->permission->isAllowed($user->role()->getValue(), $action->getValue());
	}


	private function allowActionForRole(string $action, string $role): void
	{
		$this->addRoleIfNotExists($role);
		$this->addActionIfNotExists($action);

		$this->permission->allow($role, $action);
	}


	private function addRoleIfNotExists(string $role): void
	{
		if ($this->permission->hasRole($role)) {
			return;
		}

		$this->permission->addRole($role);
	}


	private function addActionIfNotExists(string $action): void
	{
		if ($this->permission->hasResource($action)) {
			return;
		}

		$this->permission->addResource($action);
	}
}
