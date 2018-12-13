<?php

declare(strict_types=1);

namespace Caloriary\Calories\ReadModel;

use Caloriary\Calories\Value\Calories;
use Caloriary\Calories\Value\MealDescription;

interface GetCaloriesForMeal
{
    public function __invoke(MealDescription $meal): Calories;
}
