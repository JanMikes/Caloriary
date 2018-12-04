<?php declare (strict_types=1);

namespace Caloriary\Calories;

use Caloriary\Authorization\Exception\RestrictedAccess;
use League\FactoryMuffin\FactoryMuffin;
use PHPUnit\Framework\TestCase;

class CaloricRecordTest extends TestCase
{
	/**
	 * @var FactoryMuffin
	 */
	protected static $fm;


	public static function setUpBeforeClass(): void
	{
		parent::setUpBeforeClass();

		static::$fm = new FactoryMuffin();
		static::$fm->loadFactories(__DIR__ . '/../factories');
	}


	public function testCreate(): void
	{

	}


	public function testCreateShouldThrowExceptionWhenNotAuthorized(): void
	{
		$this->expectException(RestrictedAccess::class);
	}


	public function testEdit(): void
	{

	}


	public function testEditShouldThrowExceptionWhenNotAuthorized(): void
	{
		$this->expectException(RestrictedAccess::class);
	}
}
