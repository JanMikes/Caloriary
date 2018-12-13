<?php

declare(strict_types=1);

namespace Caloriary\Infrastructure\Calories\ReadModel;

use Caloriary\Application\Filtering\QueryFilters;
use Caloriary\Authentication\User;
use Caloriary\Calories\CaloricRecord;
use Caloriary\Calories\ReadModel\GetListOfCaloricRecordsForUser;
use Caloriary\Infrastructure\Application\Filtering\DQLFiltering;
use Caloriary\Infrastructure\Application\Pagination\DQLPagination;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Utils\Paginator;

final class DQLGetListOfCaloricRecordsForUser implements GetListOfCaloricRecordsForUser
{
    use DQLPagination;
    use DQLFiltering;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;


    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    /**
     * @return CaloricRecord[]
     */
    public function __invoke(User $user, Paginator $paginator, QueryFilters $filters): array
    {
        $builder = $this->entityManager->createQueryBuilder()
            ->from(CaloricRecord::class, 'record')
            ->select('record')
            ->where('record.owner = :user')
            ->setParameter('user', $user)
            ->orderBy('record.ateAt', 'DESC');

        $this->applyFiltersToQueryBuilder($builder, $filters);
        $this->applyPaginationToQueryBuilder($builder, $paginator);

        return $builder->getQuery()->getResult();
    }
}
