<?php declare (strict_types=1);

namespace Caloriary\Authentication\ReadModel;

use Caloriary\Application\Filtering\QueryFilters;

interface CountUsers
{
	public function __invoke(QueryFilters $filters): int;
}
