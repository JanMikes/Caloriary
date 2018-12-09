<?php declare (strict_types=1);

namespace Caloriary\Infrastructure\Authentication\ReadModel;

use Caloriary\Authentication\ReadModel\GetListOfUsers;
use Caloriary\Authentication\User;
use Doctrine\ORM\EntityManagerInterface;

final class DQLGetUsersList implements GetListOfUsers
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
	 * @todo: filtering
	 * @todo: paging
	 *
	 * @return User[]
	 */
	public function __invoke(): array
	{
		return $this->entityManager->createQueryBuilder()
			->from(User::class, 'user')
			->select('user')
			->getQuery()
			->getResult();
	}
}
