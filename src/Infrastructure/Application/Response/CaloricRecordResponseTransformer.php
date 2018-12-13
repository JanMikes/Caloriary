<?php

declare(strict_types=1);

namespace Caloriary\Infrastructure\Application\Response;

use Caloriary\Calories\CaloricRecord;
use Caloriary\Calories\ReadModel\HasCaloriesWithinDailyLimit;

final class CaloricRecordResponseTransformer
{
    /**
     * @var HasCaloriesWithinDailyLimit
     */
    private $hasCaloriesWithinDailyLimit;


    public function __construct(HasCaloriesWithinDailyLimit $hasCaloriesWithinDailyLimit)
    {
        $this->hasCaloriesWithinDailyLimit = $hasCaloriesWithinDailyLimit;
    }


    /**
     * @return mixed[]
     */
    public function toArray(CaloricRecord $caloricRecord): array
    {
        return [
            'id' => $caloricRecord->id()->toString(),
            'date' => $caloricRecord->ateAt()->format('Y-m-d'),
            'time' => $caloricRecord->ateAt()->format('H:i'),
            'calories' => $caloricRecord->calories()->toInteger(),
            'text' => $caloricRecord->text()->toString(),
            'withinLimit' => $this->hasCaloriesWithinDailyLimit->__invoke($caloricRecord),
        ];
    }
}
