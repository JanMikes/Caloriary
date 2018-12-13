<?php

declare(strict_types=1);

namespace Caloriary\Calories\ReadModel;

use Caloriary\Calories\CaloricRecord;

interface HasCaloriesWithinDailyLimit
{
    public function __invoke(CaloricRecord $record): bool;
}
