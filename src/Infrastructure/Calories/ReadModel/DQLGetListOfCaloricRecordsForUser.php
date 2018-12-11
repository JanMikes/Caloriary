<?php declare (strict_types=1);

namespace Caloriary\Infrastructure\Calories\ReadModel;

use Caloriary\Application\Pagination\PaginationAwareQuery;
use Caloriary\Authentication\User;
use Caloriary\Calories\CaloricRecord;
use Caloriary\Calories\ReadModel\GetListOfCaloricRecordsForUser;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Utils\Paginator;

final class DQLGetListOfCaloricRecordsForUser implements GetListOfCaloricRecordsForUser, PaginationAwareQuery
{
	/**
	 * @var EntityManagerInterface
	 */
	private $entityManager;

	/**
	 * @var Paginator|null
	 */
	private $paginator;


	public function __construct(EntityManagerInterface $entityManager)
	{
		$this->entityManager = $entityManager;
	}


	/**
	 * @todo: filtering
	 *
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

		if ($this->paginator) {
			$builder
				->setMaxResults($this->paginator->getItemsPerPage())
				->setFirstResult($this->paginator->getOffset());

			// This should prevent bugs, pagination will be valid only for single Query
			$this->paginator = null;
		}

		return $builder->getQuery()->getResult();
	}


	public function applyPaginatorForNextQuery(Paginator $paginator): void
	{
		$this->paginator = $paginator;
	}
}
