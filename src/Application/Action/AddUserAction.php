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
use Caloriary\Authorization\ACL\CanUserPerformAction;
use Caloriary\Authorization\Exception\RestrictedAccess;
use Caloriary\Calories\Value\DailyCaloriesLimit;
use Caloriary\Infrastructure\Application\Response\ResponseFormatter;
use Caloriary\Infrastructure\Application\Response\UserResponseTransformer;

final class AddUserAction implements ActionHandler
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

	/**
	 * @var CanUserPerformAction
	 */
	private $canUserPerformAction;

	/**
	 * @var UserResponseTransformer
	 */
	private $userResponseTransformer;


	public function __construct(
		ResponseFormatter $responseFormatter,
		IsEmailRegistered $isEmailRegistered,
		Users $users,
		CanUserPerformAction $canUserPerformAction,
		UserResponseTransformer $userResponseTransformer
	)
	{
		$this->responseFormatter = $responseFormatter;
		$this->isEmailRegistered = $isEmailRegistered;
		$this->users = $users;
		$this->canUserPerformAction = $canUserPerformAction;
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
			$emailAddress = EmailAddress::fromString($body->email ?? '');
			$password = ClearTextPassword::fromString($body->password ?? '');
			$dailyLimit = DailyCaloriesLimit::fromInteger($body->dailyLimit ?? 0);

			$user = User::createByAdmin(
				$emailAddress,
				$password,
				$dailyLimit,
				$currentUser,
				$this->isEmailRegistered,
				$this->canUserPerformAction
			);

			$this->users->add($user);
		}

		catch (\InvalidArgumentException $e) {
			return $this->responseFormatter->formatError($response, $e->getMessage());
		}

		catch (EmailAddressAlreadyRegistered $e) {
			$message = sprintf('Email %s is already registered', $e->emailAddress()->toString());

			return $this->responseFormatter->formatError($response, $message);
		}

		catch (RestrictedAccess $e) {
			return $this->responseFormatter->formatError($response, 'Not allowed', 403);
		}

		return $response->withJson($this->userResponseTransformer->toArray($user), 201);
	}
}
