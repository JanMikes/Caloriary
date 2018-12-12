<?php declare (strict_types=1);

namespace Caloriary\Application\Filtering;

interface FilteringAwareQuery
{
	public function applyFiltersForNextQuery(QueryFilters $filters): void;
}
