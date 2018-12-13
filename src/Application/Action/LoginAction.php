<?php declare (strict_types=1);

namespace Caloriary\Application\Action;

use BrandEmbassy\Slim\ActionHandler;
use BrandEmbassy\Slim\Request\RequestInterface;
use BrandEmbassy\Slim\Response\ResponseInterface;
use Caloriary\Authentication\Exception\AuthenticationFailed;
use Caloriary\Authentication\Exception\UserNotFound;
use Caloriary\Authentication\Repository\Users;
use Caloriary\Authentication\Token\IssueToken;
use Caloriary\Authentication\Value\ClearTextPassword;
use Caloriary\Authentication\Value\EmailAddress;
use Caloriary\Infrastructure\Application\Response\ResponseFormatter;
use Caloriary\Infrastructure\Application\Response\UserResponseTransformer;

final class LoginAction implements ActionHandler
{
	/**
	 * @var Users
	 */
	private $users;

	/**
	 * @var ResponseFormatter
	 */
	private $responseFormatter;

	/**
	 * @var IssueToken
	 */
	private $issueToken;

	/**
	 * @var UserResponseTransformer
	 */
	private $userResponseTransformer;


	public function __construct(
		Users $users,
		ResponseFormatter $responseFormatter,
		IssueToken $issueToken,
		UserResponseTransformer $userResponseTransformer
	)
	{
		$this->users = $users;
		$this->responseFormatter = $responseFormatter;
		$this->issueToken = $issueToken;
		$this->userResponseTransformer = $userResponseTransformer;
	}


	/**
	 * @param string[] $arguments
	 */
	public function __invoke(RequestInterface $request, ResponseInterface $response, array $arguments = []): ResponseInterface
	{
		$body = $request->getDecodedJsonFromBody();

		try {
			$emailAddress = EmailAddress::fromString($body->email ?? '');
			$password = ClearTextPassword::fromString($body->password ?? '');
			$user = $this->users->get($emailAddress);

			$user->authenticate($password);

			$token = $this->issueToken->__invoke($emailAddress, $request->getUri()->getHost());

			return $response->withJson(
				$this->userResponseTransformer->toArray($user) + ['token' => $token],
				201
			);
		}

		catch (\InvalidArgumentException $e) {
			return $this->responseFormatter->formatError($response, $e->getMessage());
		}

		catch (UserNotFound $e) {
			return $this->createAuthenticationFailedResponse($response);
		}

		catch (AuthenticationFailed $e) {
			return $this->createAuthenticationFailedResponse($response);
		}
	}


	private function createAuthenticationFailedResponse(ResponseInterface $response): ResponseInterface
	{
		return $this->responseFormatter->formatError($response, 'Invalid combination of credentials', 401);
	}
}
