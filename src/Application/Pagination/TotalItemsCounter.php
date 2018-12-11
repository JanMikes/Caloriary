<?php declare (strict_types=1);

namespace Caloriary\Application\Pagination;

interface TotalItemsCounter
{
	public function __invoke(): int;
}
