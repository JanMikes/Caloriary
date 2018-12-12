<?php declare (strict_types=1);

namespace Caloriary\Infrastructure\Calories\ReadModel;

use Caloriary\Application\Filtering\FilteringAwareQuery;
use Caloriary\Application\Pagination\PaginationAwareQuery;
use Caloriary\Authentication\User;
use Caloriary\Calories\CaloricRecord;
use Caloriary\Calories\ReadModel\GetListOfCaloricRecordsForUser;
use Caloriary\Infrastructure\Application\Filtering\DQLFiltering;
use Caloriary\Infrastructure\Application\Pagination\DQLPagination;
use Doctrine\ORM\EntityManagerInterface;

final class DQLGetListOfCaloricRecordsForUser implements GetListOfCaloricRecordsForUser, PaginationAwareQuery, FilteringAwareQuery
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
	public function __invoke(User $user): array
	{
		$builder = $this->entityManager->createQueryBuilder()
			->from(CaloricRecord::class, 'record')
			->select('record')
			->where('record.owner = :user')
			->setParameter('user', $user)
			->orderBy('record.ateAt', 'DESC');

		$this->applyFiltersToQueryBuilder($builder);
		$this->applyPaginationToQueryBuilder($builder);

		return $builder->getQuery()->getResult();
	}
}
