<?php declare (strict_types=1);

namespace Caloriary\Application\Action;

use BrandEmbassy\Slim\ActionHandler;
use BrandEmbassy\Slim\Request\RequestInterface;
use BrandEmbassy\Slim\Response\ResponseInterface;
use Caloriary\Authentication\Exception\UserNotFound;
use Caloriary\Authentication\Repository\Users;
use Caloriary\Authentication\Value\EmailAddress;
use Caloriary\Authorization\ACL\CanUserPerformAction;
use Caloriary\Authorization\Exception\RestrictedAccess;
use Caloriary\Authorization\Value\UserAction;
use Caloriary\Infrastructure\Application\Response\ResponseFormatter;
use Caloriary\Infrastructure\Authentication\UserProvider;

final class DeleteUserAction implements ActionHandler
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
	 * @var CanUserPerformAction
	 */
	private $canUserPerformAction;

	/**
	 * @var UserProvider
	 */
	private $userProvider;


	public function __construct(
		ResponseFormatter $responseFormatter,
		Users $users,
		CanUserPerformAction $canUserPerformAction,
		UserProvider $userProvider
	)
	{
		$this->responseFormatter = $responseFormatter;
		$this->users = $users;
		$this->canUserPerformAction = $canUserPerformAction;
		$this->userProvider = $userProvider;
	}


	/**
	 * @param string[] $arguments
	 */
	public function __invoke(RequestInterface $request, ResponseInterface $response, array $arguments = []): ResponseInterface
	{
		try {
			$currentUser = $this->userProvider->currentUser();
			$user = $this->users->get(
				EmailAddress::fromString($arguments['email'] ?? '')
			);
			$action = UserAction::get(UserAction::DELETE_USER);

			if (! $this->canUserPerformAction->__invoke($currentUser, $action)) {
				throw new RestrictedAccess();
			}

			$this->users->remove($user);

			return $response->withJson([
				'success' => true,
			], 200);
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
	}
}
