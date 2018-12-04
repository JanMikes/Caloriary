<?php declare (strict_types=1);

namespace Caloriary\Authorization\Value;

use Consistence\Enum\Enum;

class UserRole extends Enum
{
	public const USER = 'user';

	public const USER_MANAGER = 'user_manager';

	public const ADMIN = 'admin';
}
