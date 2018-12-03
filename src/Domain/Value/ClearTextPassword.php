<?php declare (strict_types=1);

namespace Caloriary\Domain\Value;

final class ClearTextPassword
{
	public function makeHash(): PasswordHash
	{
		return new PasswordHash();
	}
}
