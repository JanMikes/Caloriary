<?php declare (strict_types=1);

namespace Caloriary\Infrastructure\Authentication\ReadModel;

use Caloriary\Application\Filtering\Exception\InvalidFilterQuery;
use Caloriary\Application\Filtering\FilteringAwareQuery;
use Caloriary\Authentication\ReadModel\CountUsers;
use Caloriary\Authentication\User;
use Caloriary\Infrastructure\Application\Filtering\DQLFiltering;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\QueryException;

final class DQLCountUsers implements CountUsers, FilteringAwareQuery
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


	public function __invoke(): int
	{
		$builder = $this->entityManager->createQueryBuilder()
			->from(User::class, 'user')
			->select('COUNT(user.emailAddress)');

		try {
			$this->applyFiltersToQueryBuilder($builder);

			return (int) $builder
				->getQuery()
				->setMaxResults(1)
				->getSingleScalarResult();
		} catch (QueryException $e) {
			throw InvalidFilterQuery::fromQueryException($e);
		}
	}
}
