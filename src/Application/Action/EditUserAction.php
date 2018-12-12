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


	public function __construct(
		ResponseFormatter $responseFormatter,
		Users $users,
		CanUserPerformActionOnResource $canUserPerformActionOnResource,
		ObjectManager $manager,
		UserResponseTransformer $userResponseTransformer
	)
	{
		$this->responseFormatter = $responseFormatter;
		$this->users = $users;
		$this->canUserPerformActionOnResource = $canUserPerformActionOnResource;
		$this->manager = $manager;
		$this->userResponseTransformer = $userResponseTransformer;
	}


	public function __invoke(RequestInterface $request, ResponseInterface $response, array $arguments = []): ResponseInterface
	{
		// @TODO: Validate body, via middleware?
		// @TODO: Transform into DTO, so we have strict types

		try {
			$body = $request->getDecodedJsonFromBody();
			// @TODO: get user from attributes (set it via middleware)
			$currentUser = $this->users->get(
				EmailAddress::fromString($request->getAttribute('token')['sub'])
			);
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
		}

		catch (\InvalidArgumentException $e) {
			return $this->responseFormatter->formatError($response, $e->getMessage());
		}

		catch (RestrictedAccess $e) {
			return $this->responseFormatter->formatError($response, 'Not allowed', 403);
		}

		return $response->withJson($this->userResponseTransformer->toArray($user), 200);
	}
}
