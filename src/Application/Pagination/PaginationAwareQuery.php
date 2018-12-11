<?php declare (strict_types=1);

namespace Caloriary\Application\Pagination;

use Nette\Utils\Paginator;

interface PaginationAwareQuery
{
	public function applyPaginatorForNextQuery(Paginator $paginator): void;
}
