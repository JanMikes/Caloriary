<?php declare (strict_types=1);

namespace Caloriary\Infrastructure\Calories\ReadModel;

use Caloriary\Application\Filtering\QueryFilters;
use Caloriary\Authentication\User;
use Caloriary\Calories\CaloricRecord;
use Caloriary\Calories\ReadModel\CountCaloricRecordsOfUser;
use Caloriary\Infrastructure\Application\Filtering\DQLFiltering;
use Doctrine\ORM\EntityManagerInterface;

final class DQLCountCaloricRecordsOfUser implements CountCaloricRecordsOfUser
{
	use DQLFiltering;

	/**
	 * @var EntityManagerInterface
	 */
	private $entityManager;


	public function __construct(EntityManagerInterface $entityManager)
	{
		$this->entityManager = $entityManager;
	}


	public function __invoke(User $user, QueryFilters $filters): int
	{
		$builder = $this->entityManager->createQueryBuilder()
			->from(CaloricRecord::class, 'record')
			->select('COUNT(record.id)')
			->where('record.owner = :user')
			->setParameter('user', $user);

		$this->applyFiltersToQueryBuilder($builder, $filters);

		return (int) $builder->getQuery()
			->setMaxResults(1)
			->getSingleScalarResult();
	}
}
