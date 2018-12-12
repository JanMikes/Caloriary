<?php declare (strict_types=1);

namespace Caloriary\Application\Filtering;

final class QueryFilters
{
	/**
	 * @var string
	 */
	private $dql;

	/**
	 * @var array
	 */
	private $parameters;


	public function __construct(string $dql, array $parameters)
	{
		$this->dql = $dql;
		$this->parameters = $parameters;
	}


	public function dql(): string
	{
		return $this->dql;
	}


	public function parameters(): array
	{
		return $this->parameters;
	}


	public function hasFilters(): bool
	{
		return count($this->parameters) > 0;
	}
}
