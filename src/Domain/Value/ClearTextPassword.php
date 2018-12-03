<?php declare (strict_types=1);

namespace Caloriary\Domain\Value;

final class ClearTextPassword
{
	/**
	 * @var string
	 */
	private $password;


	/**
	 * @throws \InvalidArgumentException
	 */
	public static function fromString(string $string): self
	{
		if ('' === $string) {
			throw new \InvalidArgumentException('An empty password is not acceptable');
		}
		$instance = new self();
		$instance->password = $string;

		return $instance;
	}


	public function makeHash(): PasswordHash
	{
		return new PasswordHash();
	}


	private function __construct()
	{
	}
}
