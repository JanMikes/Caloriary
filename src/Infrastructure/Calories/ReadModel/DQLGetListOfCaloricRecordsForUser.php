<?php declare (strict_types=1);

namespace Caloriary\Infrastructure\Calories\ReadModel;

use Caloriary\Authentication\User;
use Caloriary\Calories\CaloricRecord;
use Caloriary\Calories\ReadModel\GetListOfCaloricRecordsForUser;
use Doctrine\ORM\EntityManagerInterface;

final class DQLGetListOfCaloricRecordsForUser implements GetListOfCaloricRecordsForUser
{
	/**
	 * @var EntityManagerInterface
	 */
	private $entityManager;


	public function __construct(EntityManagerInterface $entityManager)
	{
		$this->entityManager = $entityManager;
	}


	/**
	 * @todo: paging
	 * @todo: filtering
	 *
	 * @return CaloricRecord[]
	 */
	public function __invoke(User $user): array
	{
		return $this->entityManager->createQueryBuilder()
			->from(CaloricRecord::class, 'record')
			->select('record')
			->where('record.owner = :user')
			->setParameter('user', $user)
			->orderBy('record.ateAt', 'DESC')
			->getQuery()
			->getResult();
	}
}
