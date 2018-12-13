<?php

declare(strict_types=1);

namespace Caloriary\Infrastructure\Application\Filtering;

use Caloriary\Application\Filtering\QueryFilters;
use Doctrine\ORM\QueryBuilder;

trait DQLFiltering
{
    private function applyFiltersToQueryBuilder(QueryBuilder $builder, QueryFilters $filters): void
    {
        if ($filters->hasFilters()) {
            $builder->andWhere($filters->dql());

            foreach ($filters->parameters() as $key => $value) {
                $builder->setParameter($key, $value);
            }
        }
    }
}
