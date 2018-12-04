<?php declare (strict_types=1);

namespace Caloriary\Calories\Value;

class DailyCaloriesLimit
{
	/**
	 * @var int
	 */
	private $limit;


	/**
	 * @throws \InvalidArgumentException
	 */
	public static function fromInteger(int $limit): self
	{
		if ($limit < 0) {
			throw new \InvalidArgumentException('Limit of daily calories must not be lower than 0.');
		}

		$instance = new self;
		$instance->limit = $limit;

		return $instance;
	}


	public function toInteger(): int
	{
		return $this->limit;
	}


	private function __construct()
	{
	}
}
