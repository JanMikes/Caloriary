<?php declare (strict_types=1);

namespace Tests\Caloriary\Calories;

use Caloriary\Authentication\User;
use Caloriary\Authorization\Exception\RestrictedAccess;
use Caloriary\Calories\CaloricRecord;
use Caloriary\Calories\Value\CaloricRecordId;
use Caloriary\Calories\Value\Calories;
use Caloriary\Calories\Value\MealDescription;
use League\FactoryMuffin\FactoryMuffin;
use PHPUnit\Framework\TestCase;
use Tests\Caloriary\AuthorizationMockFactoryMethods;

class CaloricRecordTest extends TestCase
{
	use AuthorizationMockFactoryMethods;

	/**
	 * @var FactoryMuffin
	 */
	protected static $fm;


	public static function setUpBeforeClass(): void
	{
		parent::setUpBeforeClass();

		static::$fm = new FactoryMuffin();
		static::$fm->loadFactories(__DIR__ . '/../../factories');
	}


	public function testCreate(): void
	{
		$record = CaloricRecord::create(
			\Mockery::mock(CaloricRecordId::class),
			\Mockery::mock(User::class),
			\Mockery::mock(Calories::class),
			\Mockery::mock(\DateTimeImmutable::class),
			\Mockery::mock(MealDescription::class),
			$this->createCanPerformActionMock(true)
		);

		$this->assertInstanceOf(CaloricRecord::class, $record);
	}


	public function testCreateShouldThrowExceptionWhenNotAuthorized(): void
	{
		$this->expectException(RestrictedAccess::class);

		CaloricRecord::create(
			\Mockery::mock(CaloricRecordId::class),
			\Mockery::mock(User::class),
			\Mockery::mock(Calories::class),
			\Mockery::mock(\DateTimeImmutable::class),
			\Mockery::mock(MealDescription::class),
			$this->createCanPerformActionMock(false)
		);
	}


	/**
	 * @doesNotPerformAssertions
	 */
	public function testEdit(): void
	{
		/** @var CaloricRecord $record */
		$record = self::$fm->instance(CaloricRecord::class);

		$record->edit(
			\Mockery::mock(Calories::class),
			\Mockery::mock(\DateTimeImmutable::class),
			\Mockery::mock(MealDescription::class),
			\Mockery::mock(User::class),
			$this->createCanPerformActionOnResourceMock(true)
		);
	}


	public function testEditShouldThrowExceptionWhenNotAuthorized(): void
	{
		$this->expectException(RestrictedAccess::class);

		/** @var CaloricRecord $record */
		$record = self::$fm->instance(CaloricRecord::class);

		$record->edit(
			\Mockery::mock(Calories::class),
			\Mockery::mock(\DateTimeImmutable::class),
			\Mockery::mock(MealDescription::class),
			\Mockery::mock(User::class),
			$this->createCanPerformActionOnResourceMock(false)
		);
	}
}
