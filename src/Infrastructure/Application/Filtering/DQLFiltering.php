<?php declare (strict_types=1);

namespace Caloriary\Infrastructure\Application\Filtering;

use Caloriary\Application\Filtering\QueryFilters;
use Doctrine\ORM\QueryBuilder;

trait DQLFiltering
{
	/**
	 * @var QueryFilters|null
	 */
	private $filters;


	public function applyFiltersForNextQuery(QueryFilters $filters): void
	{
		$this->filters = $filters;
	}


	private function applyFiltersToQueryBuilder(QueryBuilder $builder): void
	{
		if ($this->filters && $this->filters->hasFilters()) {
			$builder->andWhere($this->filters->dql());
			$builder->setParameters($this->filters->parameters());

			// This should prevent bugs, filters will be valid only for next Query
			$this->filters = null;
		}
	}
}
