<?php declare (strict_types=1);

namespace Caloriary\Infrastructure\Application\Pagination;

use Doctrine\ORM\QueryBuilder;
use Nette\Utils\Paginator;

trait DQLPagination
{
	/**
	 * @var Paginator|null
	 */
	private $paginator;


	public function applyPaginatorForNextQuery(Paginator $paginator): void
	{
		$this->paginator = $paginator;
	}


	private function applyPaginationToQueryBuilder(QueryBuilder $builder): void
	{
		if ($this->paginator) {
			$builder
				->setMaxResults($this->paginator->getItemsPerPage())
				->setFirstResult($this->paginator->getOffset());

			// This should prevent bugs, pagination will be valid only for next Query
			$this->paginator = null;
		}
	}
}
