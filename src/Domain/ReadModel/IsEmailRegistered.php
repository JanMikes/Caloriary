<?php declare (strict_types=1);

namespace Caloriary\Domain\ReadModel;

use Caloriary\Domain\Value\EmailAddress;

interface IsEmailRegistered
{
	public function __invoke(EmailAddress $emailAddress): bool;
}
