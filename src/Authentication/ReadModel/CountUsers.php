<?php declare (strict_types=1);

namespace Caloriary\Authentication\ReadModel;

interface CountUsers
{
	public function __invoke(): int;
}
