<?php declare (strict_types=1);

namespace Caloriary\Calories\ReadModel;

use Caloriary\Authentication\User;

interface CountCaloricRecordsOfUser
{
	public function __invoke(User $user): int;
}
