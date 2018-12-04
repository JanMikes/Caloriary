<?php declare (strict_types=1);

namespace Caloriary\Calories\Repository;

use Caloriary\Calories\CaloricRecord;
use Caloriary\Calories\Value\CaloricRecordId;

interface CaloricRecords
{
	public function get(CaloricRecordId $recordId): CaloricRecord;

	public function add(CaloricRecord $record): void;

	public function remove(CaloricRecord $caloricRecord): void;
}
