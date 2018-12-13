<?php declare (strict_types=1);

namespace Caloriary\Application\Filtering;

final class QueryFilters
{
	/**
	 * @var string
	 */
	private $dql;

	/**
	 * @var mixed[]
	 */
	private $parameters;


	/**
	 * @param mixed[] $parameters
	 */
	public function __construct(string $dql, array $parameters)
	{
		$this->dql = $dql;
		$this->parameters = $parameters;
	}


	public function dql(): string
	{
		return $this->dql;
	}


	/**
	 * @return mixed[]
	 */
	public function parameters(): array
	{
		return $this->parameters;
	}


	public function hasFilters(): bool
	{
		return count($this->parameters) > 0;
	}
}
