<?php declare (strict_types=1);

namespace Caloriary;

use Nette\Utils\Paginator;

interface PaginationInterface
{
	public function applyPaginator(Paginator $paginator): void;
}
