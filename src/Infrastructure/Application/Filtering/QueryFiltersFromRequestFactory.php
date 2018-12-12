<?php declare (strict_types=1);

namespace Caloriary\Infrastructure\Application\Filtering;

use BrandEmbassy\Slim\Request\RequestInterface;
use Caloriary\Application\Filtering\Exception\InvalidFilterQuery;
use Caloriary\Application\Filtering\QueryFilters;

final class QueryFiltersFromRequestFactory
{
	public const PATTERN = '/(?<field>\w*) (?<operator>eq|ne|gt|lt|lte|gte*) (?<value>\'\S*\'|\d*)/';

	public const OPERATIONS = [
		'eq' => '=',
		'ne' => '!=',
		'lt' => '<',
		'gt' => '>',
		'lte' => '<=',
		'gte' => '>=',
	];

	public const FIELDS = [
		'email' => 'user.emailAddress',
	];


	/**
	 * @throws InvalidFilterQuery
	 */
	public function create(RequestInterface $request): QueryFilters
	{
		$query = $request->getQueryParam('filter', '');
		$iteration = 1;
		$parameters = [];

		$query = preg_replace_callback(self::PATTERN, function($match) use (&$iteration, &$parameters) {
			$value = $match['value'];
			$field = $this->replaceFieldWithEntityField($match['field']);
			$operator = $this->replaceOperationWithOperator($match['operator']);

			$parameters['queryParam' . $iteration] = is_numeric($value) ? (float) $value : trim($value, '\'');
			$value = ':queryParam' . $iteration;

			$iteration++;

			return "$field $operator $value";
		}, $query);

		return new QueryFilters($query, $parameters);
	}


	/**
	 * @throws InvalidFilterQuery
	 */
	private function replaceOperationWithOperator(string $operation): string
	{
		if (!isset(self::OPERATIONS[$operation])) {
			throw new InvalidFilterQuery(sprintf(
				'Unknown operation \'%s\'',
				$operation
			));
		}

		return self::OPERATIONS[$operation];
	}


	/**
	 * @throws InvalidFilterQuery
	 */
	private function replaceFieldWithEntityField(string $field): string
	{
		if (!isset(self::FIELDS[$field])) {
			throw new InvalidFilterQuery(sprintf(
				'Unknown field \'%s\'',
				$field
			));
		}

		return self::OPERATIONS[$field];
	}
}
