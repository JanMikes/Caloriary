<?php declare (strict_types=1);

namespace Caloriary\Infrastructure\Application\Response;

use Caloriary\Authentication\User;

final class UserResponseTransformer
{
	public function toArray(User $user): array
	{
		return [
			'email' => $user->emailAddress()->toString(),
			'dailyLimit' => $user->dailyLimit()->toInteger(),
		];
	}
}
