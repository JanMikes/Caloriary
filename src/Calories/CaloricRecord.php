<?php declare (strict_types=1);

namespace Caloriary\Calories;

use Caloriary\Authentication\User;
use Caloriary\Calories\Value\CaloricRecordId;
use Caloriary\Calories\Value\Calories;
use Caloriary\Calories\Value\MealDescription;

final class CaloricRecord
{
	/**
	 * @var CaloricRecordId
	 */
	private $id;

	/**
	 * @var User
	 */
	private $user;

	/**
	 * @var Calories
	 */
	private $calories;

	/**
	 * @var \DateTimeImmutable
	 */
	private $date;

	/**
	 * @var MealDescription
	 */
	private $text;


	public function edit(
		$calories,
		$date,
		$text
	): void
	{
		// 1. only own calory entry OR admin
		// 2. user managers are not permitted to edit
	}


	public function __construct(
		CaloricRecordId $id,
		User $user,
		Calories $calories,
		\DateTimeImmutable $date,
		MealDescription $text
	)
	{
		$this->id = $id;
		$this->user = $user;
		$this->calories = $calories;
		$this->date = $date;
		$this->text = $text;
	}
}
