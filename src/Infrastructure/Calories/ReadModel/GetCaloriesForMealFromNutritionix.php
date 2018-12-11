<?php declare (strict_types=1);

namespace Caloriary\Infrastructure\Calories\ReadModel;

use Caloriary\Calories\Exception\MealNotFound;
use Caloriary\Calories\ReadModel\GetCaloriesForMeal;
use Caloriary\Calories\Value\Calories;
use Caloriary\Calories\Value\MealDescription;
use JanMikes\Nutritionix\Nutritionix;

final class GetCaloriesForMealFromNutritionix implements GetCaloriesForMeal
{
	/**
	 * @var Nutritionix
	 */
	private $nutritionix;


	public function __construct(Nutritionix $nutritionix)
	{
		$this->nutritionix = $nutritionix;
	}


	public function __invoke(MealDescription $meal): Calories
	{
		$calories = 0;
		$foods = $this->nutritionix->searchForFoods($meal->toString());

		if (count($foods) === 0) {
			throw new MealNotFound($meal);
		}

		foreach ($foods as $food) {
			$calories += $food->calories();
		}

		return Calories::fromInteger($calories);
	}
}
