<?php declare (strict_types=1);

namespace Caloriary\Infrastructure\Authentication\ReadModel;

use Caloriary\Authentication\ReadModel\GetListOfUsers;
use Caloriary\Authentication\User;
use Caloriary\PaginationInterface;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Utils\Paginator;

final class DQLGetListOfUsers implements GetListOfUsers, PaginationInterface
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
	 * @return User[]
	 */
	public function __invoke(): array
	{
		$builder = $this->entityManager->createQueryBuilder()
			->from(User::class, 'user')
			->select('user');

		if ($this->paginator) {
			$builder
				->setMaxResults($this->paginator->getItemsPerPage())
				->setFirstResult($this->paginator->getOffset());
		}

		return $builder->getQuery()->getResult();
	}


	public function applyPaginator(Paginator $paginator): void
	{
		$this->paginator = $paginator;
	}
}
