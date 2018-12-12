<?php declare (strict_types=1);

namespace Caloriary\Infrastructure\Authentication\ReadModel;

use Caloriary\Application\Filtering\Exception\InvalidFilterQuery;
use Caloriary\Application\Filtering\FilteringAwareQuery;
use Caloriary\Authentication\ReadModel\GetListOfUsers;
use Caloriary\Authentication\User;
use Caloriary\Application\Pagination\PaginationAwareQuery;
use Caloriary\Infrastructure\Application\Filtering\DQLFiltering;
use Caloriary\Infrastructure\Application\Pagination\DQLPagination;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\QueryException;

final class DQLGetListOfUsers implements GetListOfUsers, PaginationAwareQuery, FilteringAwareQuery
{
	use DQLFiltering;
	use DQLPagination;

	/**
	 * @var EntityManagerInterface
	 */
	private $entityManager;


	public function __construct(EntityManagerInterface $entityManager)
	{
		$this->entityManager = $entityManager;
	}


	/**
	 * @return User[]
	 *
	 * @throws QueryException
	 */
	public function __invoke(): array
	{
		$builder = $this->entityManager->createQueryBuilder()
			->from(User::class, 'user')
			->select('user');

		$this->applyPaginationToQueryBuilder($builder);

		try {
			$this->applyFiltersToQueryBuilder($builder);

			return $builder->getQuery()->getResult();
		} catch (QueryException $e) {
			throw InvalidFilterQuery::fromQueryException($e);
		}
	}
}
