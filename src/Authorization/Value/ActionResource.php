<?php declare (strict_types=1);

namespace Caloriary\Authorization\Value;

use Consistence\Enum\Enum;

final class ActionResource extends Enum
{
	public const EDIT_USER = 'edit_user';

	public const CHANGE_USER_PASSWORD = 'change_user_password';

	public const CHANGE_USER_ROLE = 'change_user_role';
}
