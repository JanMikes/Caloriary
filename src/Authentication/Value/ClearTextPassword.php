<?php declare (strict_types=1);

namespace Caloriary\Authentication\Value;

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
		$hash = password_hash($this->password, \PASSWORD_DEFAULT);

		if (\is_string($hash) === false) {
			throw new \RuntimeException('Failed to create password hash');
		}

		return PasswordHash::fromString($hash);
	}


	public function matches(PasswordHash $hash): bool
	{
		return password_verify($this->password, $hash->toString());
	}


	private function __construct()
	{
	}
}
