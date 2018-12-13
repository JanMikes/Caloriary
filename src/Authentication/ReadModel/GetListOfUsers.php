<?php

declare(strict_types=1);

namespace Caloriary\Authentication\ReadModel;

use Caloriary\Application\Filtering\QueryFilters;
use Caloriary\Authentication\User;
use Nette\Utils\Paginator;

interface GetListOfUsers
{
    /**
     * @return User[]
     */
    public function __invoke(Paginator $paginator, QueryFilters $filters): array;
}
