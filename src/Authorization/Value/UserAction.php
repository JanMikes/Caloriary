<?php declare (strict_types=1);

namespace Caloriary\Authorization\Value;

use Consistence\Enum\Enum;

final class UserAction extends Enum
{
	public const EDIT_USER = 'edit_user';

	public const CHANGE_USER_PASSWORD = 'change_user_password';

	public const CHANGE_USER_ROLE = 'change_user_role';

	public const CREATE_CALORIC_RECORD = 'create_caloric_record';

	public const EDIT_CALORIC_RECORD = 'edit_caloric_record';

	public const ADD_USER = 'add_user';

	public const DELETE_CALORIC_RECORD = 'delete_caloric_record';

	public const DELETE_USER = 'delete_user';

	public const USER_DETAIL = 'user_detail';

	public const LIST_USERS = 'list_users';

	public const LIST_CALORIC_RECORDS = 'list_caloric_records';

	public const CALORIC_RECORD_DETAIL = 'caloric_record_detail';
}
