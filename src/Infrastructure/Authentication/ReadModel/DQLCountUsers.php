<?php declare (strict_types=1);

namespace Caloriary\Infrastructure\Authentication\ReadModel;

use Caloriary\Authentication\ReadModel\CountUsers;
use Caloriary\Authentication\User;
use Doctrine\ORM\EntityManagerInterface;

final class DQLCountUsers implements CountUsers
{
	/**
	 * @var EntityManagerInterface
	 */
	private $entityManager;


	public function __construct(EntityManagerInterface $entityManager)
	{
		$this->entityManager = $entityManager;
	}


	public function __invoke(): int
	{
		return (int) $this->entityManager->createQueryBuilder()
			->from(User::class, 'user')
			->select('COUNT(user.emailAddress)')
			->getQuery()
			->setMaxResults(1)
			->getSingleScalarResult();
	}
}
