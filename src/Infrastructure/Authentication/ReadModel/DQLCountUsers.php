<?php declare (strict_types=1);

namespace Caloriary\Infrastructure\Authentication\ReadModel;

use Caloriary\Application\Filtering\Exception\InvalidFilterQuery;
use Caloriary\Application\Filtering\FilteringAwareQuery;
use Caloriary\Application\Filtering\QueryFilters;
use Caloriary\Authentication\ReadModel\CountUsers;
use Caloriary\Authentication\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\QueryException;

final class DQLCountUsers implements CountUsers, FilteringAwareQuery
{
	/**
	 * @var EntityManagerInterface
	 */
	private $entityManager;

	/**
	 * @var QueryFilters|null
	 */
	private $filters;


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
			if ($this->filters) {
				$builder->andWhere($this->filters->dql());
				$builder->setParameters($this->filters->parameters());

				// This should prevent bugs, filters will be valid only for next Query
				$this->filters = null;
			}

			return (int) $builder
				->getQuery()
				->setMaxResults(1)
				->getSingleScalarResult();
		} catch (QueryException $e) {
			throw InvalidFilterQuery::fromQueryException($e);
		}
	}


	public function applyFiltersForNextQuery(QueryFilters $filters): void
	{
		$this->filters = $filters;
	}
}
