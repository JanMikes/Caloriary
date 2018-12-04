<?php declare (strict_types=1);

namespace Caloriary\Calories\Value;

final class MealDescription
{
	private $text;


	/**
	 * @throws \InvalidArgumentException
	 */
	public static function fromString(string $text): self
	{
		if (trim($text) === '') {
			throw new \InvalidArgumentException('Empty meal description is not accepted.');
		}

		$instance = new self;
		$instance->text = $text;

		return $instance;
	}


	public function toString(): string
	{
		return $this->text;
	}


	private function __construct()
	{
	}
}