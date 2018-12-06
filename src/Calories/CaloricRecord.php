<?php declare (strict_types=1);

namespace Caloriary\Calories;

use Caloriary\Authentication\User;
use Caloriary\Authorization\Exception\RestrictedAccess;
use Caloriary\Authorization\ReadModel\CanUserPerformAction;
use Caloriary\Authorization\ReadModel\CanUserPerformActionOnResource;
use Caloriary\Authorization\Resource;
use Caloriary\Authorization\Value\UserAction;
use Caloriary\Calories\Value\CaloricRecordId;
use Caloriary\Calories\Value\Calories;
use Caloriary\Calories\Value\MealDescription;

class CaloricRecord implements Resource
{
	/**
	 * @var CaloricRecordId
	 */
	private $id;

	/**
	 * @var User
	 */
	private $owner;

	/**
	 * @var Calories
	 */
	private $calories;

	/**
	 * @var \DateTimeImmutable
	 */
	private $ateAt;

	/**
	 * @var MealDescription
	 */
	private $text;


	/**
	 * @throws RestrictedAccess
	 */
	public static function create(
		CaloricRecordId $id,
		User $owner,
		Calories $calories,
		\DateTimeImmutable $ateAt,
		MealDescription $text,
		CanUserPerformAction $canUserPerformAction
	): self
	{
		$action = UserAction::get(UserAction::CREATE_CALORIC_RECORD);

		if ($canUserPerformAction->__invoke($owner, $action) === false) {
			throw new RestrictedAccess();
		}

		return new self(
			$id,
			$owner,
			$calories,
			$ateAt,
			$text
		);
	}


	/**
	 * @throws RestrictedAccess
	 */
	public function edit(
		Calories $calories,
		\DateTimeImmutable $ateAt,
		MealDescription $text,
		User $editor,
		CanUserPerformActionOnResource $canUserPerformActionOnResource
	): void
	{
		$action = UserAction::get(UserAction::EDIT_CALORIC_RECORD);

		if ($canUserPerformActionOnResource->__invoke($editor, $action, $this) === false) {
			throw new RestrictedAccess();
		}

		$this->calories = $calories;
		$this->ateAt = $ateAt;
		$this->text = $text;
	}


	public function ownedBy(): User
	{
		return $this->owner;
	}


	public function id(): CaloricRecordId
	{
		return $this->id;
	}


	public function calories(): Calories
	{
		return $this->calories;
	}


	public function ateAt(): \DateTimeImmutable
	{
		return $this->ateAt;
	}


	public function text(): MealDescription
	{
		return $this->text;
	}


	private function __construct(
		CaloricRecordId $id,
		User $user,
		Calories $calories,
		\DateTimeImmutable $ateAt,
		MealDescription $text
	)
	{
		$this->id = $id;
		$this->owner = $user;
		$this->calories = $calories;
		$this->ateAt = $ateAt;
		$this->text = $text;
	}
}
