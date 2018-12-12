<?php declare (strict_types=1);

namespace Caloriary\Infrastructure\Authentication\ReadModel;

use Caloriary\Application\Filtering\Exception\InvalidFilterQuery;
use Caloriary\Application\Filtering\QueryFilters;
use Caloriary\Authentication\ReadModel\GetListOfUsers;
use Caloriary\Authentication\User;
use Caloriary\Infrastructure\Application\Filtering\DQLFiltering;
use Caloriary\Infrastructure\Application\Pagination\DQLPagination;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\QueryException;
use Nette\Utils\Paginator;

final class DQLGetListOfUsers implements GetListOfUsers
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
	public function __invoke(Paginator $paginator, QueryFilters $filters): array
	{
		$builder = $this->entityManager->createQueryBuilder()
			->from(User::class, 'user')
			->select('user');

		$this->applyPaginationToQueryBuilder($builder, $paginator);

		try {
			$this->applyFiltersToQueryBuilder($builder, $filters);

			return $builder->getQuery()->getResult();
		} catch (QueryException $e) {
			throw InvalidFilterQuery::fromQueryException($e);
		}
	}
}
