<?php

declare(strict_types=1);

namespace Caloriary\Calories\ReadModel;

use Caloriary\Application\Filtering\QueryFilters;
use Caloriary\Authentication\User;
use Caloriary\Calories\CaloricRecord;
use Nette\Utils\Paginator;

interface GetListOfCaloricRecordsForUser
{
    /**
     * @return CaloricRecord[]
     */
    public function __invoke(User $user, Paginator $paginator, QueryFilters $filters): array;
}
