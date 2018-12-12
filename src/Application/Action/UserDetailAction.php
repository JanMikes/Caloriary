<?php declare (strict_types=1);

namespace Caloriary\Application\Action;

use BrandEmbassy\Slim\ActionHandler;
use BrandEmbassy\Slim\Request\RequestInterface;
use BrandEmbassy\Slim\Response\ResponseInterface;
use Caloriary\Authentication\Exception\UserNotFound;
use Caloriary\Authentication\Repository\Users;
use Caloriary\Authentication\Value\EmailAddress;
use Caloriary\Authorization\ACL\CanUserPerformActionOnResource;
use Caloriary\Authorization\Exception\RestrictedAccess;
use Caloriary\Authorization\Value\UserAction;
use Caloriary\Infrastructure\Application\Response\ResponseFormatter;
use Caloriary\Infrastructure\Application\Response\UserResponseTransformer;

final class UserDetailAction implements ActionHandler
{
	/**
	 * @var ResponseFormatter
	 */
	private $responseFormatter;

	/**
	 * @var Users
	 */
	private $users;

	/**
	 * @var CanUserPerformActionOnResource
	 */
	private $canUserPerformActionOnResource;

	/**
	 * @var UserResponseTransformer
	 */
	private $userResponseTransformer;


	public function __construct(
		ResponseFormatter $responseFormatter,
		Users $users,
		CanUserPerformActionOnResource $canUserPerformActionOnResource,
		UserResponseTransformer $userResponseTransformer
	)
	{
		$this->responseFormatter = $responseFormatter;
		$this->users = $users;
		$this->canUserPerformActionOnResource = $canUserPerformActionOnResource;
		$this->userResponseTransformer = $userResponseTransformer;
	}


	public function __invoke(RequestInterface $request, ResponseInterface $response, array $arguments = []): ResponseInterface
	{
		try {
			// @TODO: get user from attributes (set it via middleware)
			$currentUser = $this->users->get(
				EmailAddress::fromString($request->getAttribute('token')['sub'])
			);
			$user = $this->users->get(
				EmailAddress::fromString($arguments['email'] ?? '')
			);
			$action = UserAction::get(UserAction::USER_DETAIL);

			if (! $this->canUserPerformActionOnResource->__invoke($currentUser, $action, $user)) {
				throw new RestrictedAccess();
			}
		}

		catch (\InvalidArgumentException $e) {
			return $this->responseFormatter->formatError($response, $e->getMessage());
		}

		catch (UserNotFound $e) {
			return $this->responseFormatter->formatError($response, 'User not found!', 404);
		}

		catch (RestrictedAccess $e) {
			return $this->responseFormatter->formatError($response, 'Not allowed', 403);
		}

		return $response->withJson($this->userResponseTransformer->toArray($user), 200);
	}
}
