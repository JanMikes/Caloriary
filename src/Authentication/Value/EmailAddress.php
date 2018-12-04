<?php declare (strict_types=1);

namespace Caloriary\Authentication\Value;

class EmailAddress
{
	/**
	 * @var string
	 */
	private $mail;


	/**
	 * @throws \InvalidArgumentException
	 */
	public static function fromString(string $mail): self
	{
		if (! \filter_var($mail, \FILTER_VALIDATE_EMAIL)) {
			throw new \InvalidArgumentException(sprintf(
				'Invalid email "%s" provided',
				$mail
			));
		}

		$instance = new self;
		$instance->mail = $mail;

		return $instance;
	}


	public function toString(): string
	{
		return $this->mail;
	}


	private function __construct()
	{
	}
}
