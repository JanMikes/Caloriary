<?php declare (strict_types=1);

namespace Caloriary\Calories\ReadModel;

use Caloriary\Authentication\User;
use Caloriary\Calories\CaloricRecord;

interface GetListOfCaloricRecordsForUser
{
	/**
	 * @todo: paging
	 * @todo: filtering
	 *
	 * @return CaloricRecord[]
	 */
	public function __invoke(User $user): array;
}
