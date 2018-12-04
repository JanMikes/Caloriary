<?php declare (strict_types=1);

namespace Caloriary\Calories\Value;

class Calories
{
	/**
	 * @var int
	 */
	private $calories;


	/**
	 * @throws \InvalidArgumentException
	 */
	public static function fromInteger(int $calories): self
	{
		if ($calories <= 0) {
			throw new \InvalidArgumentException('Calories must be number higher than 0.');
		}

		$instance = new self;
		$instance->calories = $calories;

		return $instance;
	}


	private function __construct()
	{
	}
}
