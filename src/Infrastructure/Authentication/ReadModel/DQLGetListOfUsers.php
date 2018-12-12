<?php declare (strict_types=1);

namespace Caloriary\Infrastructure\Authentication\ReadModel;

use Caloriary\Authentication\ReadModel\GetListOfUsers;
use Caloriary\Authentication\User;
use Caloriary\Application\Pagination\PaginationAwareQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\QueryException;
use Nette\Utils\Paginator;

final class DQLGetListOfUsers implements GetListOfUsers, PaginationAwareQuery
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

			// This should prevent bugs, pagination will be valid only for single Query
			$this->paginator = null;
		}

		$query = "(email eq 'j.mikes@me.com') AND (date eq '2016-05-01') AND ((number_of_calories gt 20) OR (number_of_calories lt 10))";

		$pattern = '/(?<field>\w*) (?<operator>eq|ne|gt|lt|lte|gte*) (?<value>\'\S*\'|\d*)/';
		$iteration = 1;
		$parameters = [];
		$query = preg_replace_callback($pattern, function($match) use (&$iteration, &$parameters) {
			$field = $match['field'];
			$operator = $match['operator'];
			$value = $match['value'];

			$parameters['queryParam' . $iteration] = is_numeric($value) ? (float) $value : trim($value, '\'');
			$value = ':queryParam' . $iteration;

			$field = str_replace('email', 'user.emailAddress', $field);

			$operator = str_replace(
				['eq', 'ne', 'lt', 'lte', 'gt', 'gte'],
				['=', '!=', '<', '<=', '>', '>='],
				$operator
			);

			$iteration++;

			return "$field $operator $value";
		}, $query);

		$builder->andWhere($query);
		$builder->setParameters($parameters);

		return $builder->getQuery()->getResult();
	}


	public function applyPaginatorForNextQuery(Paginator $paginator): void
	{
		$this->paginator = $paginator;
	}
}
