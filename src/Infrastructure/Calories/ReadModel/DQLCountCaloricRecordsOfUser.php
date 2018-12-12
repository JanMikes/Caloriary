<?php declare (strict_types=1);

namespace Caloriary\Infrastructure\Calories\ReadModel;

use Caloriary\Authentication\User;
use Caloriary\Calories\CaloricRecord;
use Caloriary\Calories\ReadModel\CountCaloricRecordsOfUser;
use Doctrine\ORM\EntityManagerInterface;

final class DQLCountCaloricRecordsOfUser implements CountCaloricRecordsOfUser
{
	/**
	 * @var EntityManagerInterface
	 */
	private $entityManager;


	public function __construct(EntityManagerInterface $entityManager)
	{
		$this->entityManager = $entityManager;
	}


	public function __invoke(User $user): int
	{
		return (int) $this->entityManager->createQueryBuilder()
			->from(CaloricRecord::class, 'record')
			->select('COUNT(record.id)')
			->where('record.owner = :user')
			->setParameter('user', $user)
			->getQuery()
			->setMaxResults(1)
			->getSingleScalarResult();
	}
}
