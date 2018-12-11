<?php declare (strict_types=1);

namespace Caloriary\Calories\Exception;

use Caloriary\Calories\Value\MealDescription;

final class MealNotFound extends \RuntimeException
{
	public function __construct(MealDescription $meal)
	{
		parent::__construct(sprintf(
			'Could not find calories for: %s',
			$meal->toString()
		));
	}
}
