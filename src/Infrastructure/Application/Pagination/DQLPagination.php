<?php declare (strict_types=1);

namespace Caloriary\Infrastructure\Application\Pagination;

use Doctrine\ORM\QueryBuilder;
use Nette\Utils\Paginator;

trait DQLPagination
{
	private function applyPaginationToQueryBuilder(QueryBuilder $builder, Paginator $paginator): void
	{
		$builder
			->setMaxResults($paginator->getItemsPerPage())
			->setFirstResult($paginator->getOffset());
	}
}
