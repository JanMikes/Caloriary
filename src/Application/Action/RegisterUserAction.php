<?php declare (strict_types=1);

namespace Caloriary\Application\Action;

use BrandEmbassy\Slim\ActionHandler;
use BrandEmbassy\Slim\Request\RequestInterface;
use BrandEmbassy\Slim\Response\ResponseInterface;
use Caloriary\Authentication\Exception\EmailAddressAlreadyRegistered;
use Caloriary\Authentication\ReadModel\IsEmailRegistered;
use Caloriary\Authentication\Repository\Users;
use Caloriary\Authentication\User;
use Caloriary\Authentication\Value\ClearTextPassword;
use Caloriary\Authentication\Value\EmailAddress;
use Caloriary\Infrastructure\Application\Response\ResponseFormatter;

final class RegisterUserAction implements ActionHandler
{
	/**
	 * @var IsEmailRegistered
	 */
	private $isEmailRegistered;

	/**
	 * @var ResponseFormatter
	 */
	private $responseFormatter;

	/**
	 * @var Users
	 */
	private $users;


	public function __construct(
		ResponseFormatter $responseFormatter,
		IsEmailRegistered $isEmailRegistered,
		Users $users
	)
	{
		$this->responseFormatter = $responseFormatter;
		$this->isEmailRegistered = $isEmailRegistered;
		$this->users = $users;
	}


	public function __invoke(RequestInterface $request, ResponseInterface $response, array $arguments = []): ResponseInterface
	{
		$body = $request->getDecodedJsonFromBody();

		// @TODO: Validate body, via middleware?
		// @TODO: Transform into DTO, so we have strict types

		try {
			$emailAddress = EmailAddress::fromString($body->email ?? '');
			$password = ClearTextPassword::fromString($body->password ?? '');

			$this->users->add(
				User::register($emailAddress, $password, $this->isEmailRegistered)
			);
		}

		catch (\InvalidArgumentException $e) {
			return $this->responseFormatter->formatError($response, $e->getMessage());
		}

		catch (EmailAddressAlreadyRegistered $e) {
			$message = sprintf('Email %s is already registered', $e->emailAddress()->toString());

			return $this->responseFormatter->formatError($response, $message);
		}

		return $response->withJson([
			'success' => true,
		], 201);
	}
}
