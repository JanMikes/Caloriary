<?php declare (strict_types=1);

namespace Caloriary\Infrastructure\Authentication\Token;

use Caloriary\Authentication\Token\IssueToken;
use Caloriary\Authentication\Value\EmailAddress;
use Firebase\JWT\JWT;

final class JsonWebTokenFactory implements IssueToken
{
	/**
	 * @var string
	 */
	private $secret;

	/**
	 * @var int
	 */
	private $validForSeconds;

	/**
	 * @var string
	 */
	private $issuer;


	public function __construct(string $issuer, string $secret, int $validForSeconds)
	{
		$this->secret = $secret;
		$this->validForSeconds = $validForSeconds;
		$this->issuer = $issuer;
	}


	public function __invoke(EmailAddress $emailAddress, string $audience): string
	{
		return JWT::encode([
			'iat' => time(),
			'nbf' => time(),
			'exp' => time() + $this->validForSeconds,
			'iss' => $this->issuer,
			'sub' => $emailAddress->toString(),
			'aud' => $audience,

		], $this->secret);
	}
}
