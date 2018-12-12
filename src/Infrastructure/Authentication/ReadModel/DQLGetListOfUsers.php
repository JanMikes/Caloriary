<?php declare (strict_types=1);

namespace Caloriary\Infrastructure\Authentication\ReadModel;

use Caloriary\Application\Filtering\Exception\InvalidFilterQuery;
use Caloriary\Application\Filtering\FilteringAwareQuery;
use Caloriary\Application\Filtering\QueryFilters;
use Caloriary\Authentication\ReadModel\GetListOfUsers;
use Caloriary\Authentication\User;
use Caloriary\Application\Pagination\PaginationAwareQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\QueryException;
use Nette\Utils\Paginator;

final class DQLGetListOfUsers implements GetListOfUsers, PaginationAwareQuery, FilteringAwareQuery
{
	/**
	 * @var EntityManagerInterface
	 */
	private $entityManager;

	/**
	 * @var Paginator|null
	 */
	private $paginator;

	/**
	 * @var QueryFilters|null
	 */
	private $filters;


	public function __construct(EntityManagerInterface $entityManager)
	{
		$this->entityManager = $entityManager;
	}


	/**
	 * @todo: filtering
	 *
	 * @return User[]
	 *
	 * @throws QueryException
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

			// This should prevent bugs, pagination will be valid only for next Query
			$this->paginator = null;
		}

		try {
			if ($this->filters) {
				$builder->andWhere($this->filters->dql());
				$builder->setParameters($this->filters->parameters());

				// This should prevent bugs, filters will be valid only for next Query
				$this->filters = null;
			}

			return $builder->getQuery()->getResult();
		} catch (QueryException $e) {
			throw InvalidFilterQuery::fromQueryException($e);
		}
	}


	public function applyPaginatorForNextQuery(Paginator $paginator): void
	{
		$this->paginator = $paginator;
	}


	public function applyFiltersForNextQuery(QueryFilters $filters): void
	{
		$this->filters = $filters;
	}
}
