<?php declare (strict_types=1);

namespace Tests\Caloriary;

use Caloriary\Authorization\ACL\CanUserPerformAction;
use Caloriary\Authorization\ACL\CanUserPerformActionOnResource;

trait AuthorizationMockFactoryMethods
{
	private function createCanPerformActionMock(bool $return): CanUserPerformAction
	{
		$canPerformAction = \Mockery::mock(CanUserPerformAction::class);
		$canPerformAction->shouldReceive('__invoke')->andReturn($return);

		return $canPerformAction;
	}


	private function createCanPerformActionOnResourceMock(bool $return): CanUserPerformActionOnResource
	{
		$canPerformActionOnResource = \Mockery::mock(CanUserPerformActionOnResource::class);
		$canPerformActionOnResource->shouldReceive('__invoke')->andReturn($return);

		return $canPerformActionOnResource;
	}
}
