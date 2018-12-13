<?php declare (strict_types=1);

namespace Caloriary\Application\Action;

use BrandEmbassy\Slim\ActionHandler;
use BrandEmbassy\Slim\Request\RequestInterface;
use BrandEmbassy\Slim\Response\ResponseInterface;
use Caloriary\Authentication\Repository\Users;
use Caloriary\Authentication\Value\ClearTextPassword;
use Caloriary\Authentication\Value\EmailAddress;
use Caloriary\Authorization\ACL\CanUserPerformActionOnResource;
use Caloriary\Authorization\Exception\RestrictedAccess;
use Caloriary\Calories\Value\DailyCaloriesLimit;
use Caloriary\Infrastructure\Application\Response\ResponseFormatter;
use Caloriary\Infrastructure\Application\Response\UserResponseTransformer;
use Caloriary\Infrastructure\Authentication\UserProvider;
use Doctrine\Common\Persistence\ObjectManager;

final class EditUserAction implements ActionHandler
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
	 * @var ObjectManager
	 */
	private $manager;

	/**
	 * @var UserResponseTransformer
	 */
	private $userResponseTransformer;

	/**
	 * @var UserProvider
	 */
	private $userProvider;


	public function __construct(
		ResponseFormatter $responseFormatter,
		Users $users,
		CanUserPerformActionOnResource $canUserPerformActionOnResource,
		ObjectManager $manager,
		UserResponseTransformer $userResponseTransformer,
		UserProvider $userProvider
	)
	{
		$this->responseFormatter = $responseFormatter;
		$this->users = $users;
		$this->canUserPerformActionOnResource = $canUserPerformActionOnResource;
		$this->manager = $manager;
		$this->userResponseTransformer = $userResponseTransformer;
		$this->userProvider = $userProvider;
	}


	/**
	 * @param string[] $arguments
	 */
	public function __invoke(RequestInterface $request, ResponseInterface $response, array $arguments = []): ResponseInterface
	{
		try {
			$body = $request->getDecodedJsonFromBody();
			$currentUser = $this->userProvider->currentUser();
			$user = $this->users->get(
				EmailAddress::fromString($arguments['email'] ?? '')
			);
			$dailyLimit = DailyCaloriesLimit::fromInteger($body->dailyLimit ?? 0);

			$currentUser->editUser(
				$user,
				$dailyLimit,
				$this->canUserPerformActionOnResource
			);

			if (isset($body->password)) {
				$currentUser->changeUserPassword(
					$user,
					ClearTextPassword::fromString($body->password),
					$this->canUserPerformActionOnResource
				);
			}

			$this->manager->flush();

			return $response->withJson($this->userResponseTransformer->toArray($user), 200);
		}

		catch (\InvalidArgumentException $e) {
			return $this->responseFormatter->formatError($response, $e->getMessage());
		}

		catch (RestrictedAccess $e) {
			return $this->responseFormatter->formatError($response, 'Not allowed', 403);
		}
	}
}
