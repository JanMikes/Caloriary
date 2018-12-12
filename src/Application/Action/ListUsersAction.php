<?php declare (strict_types=1);

namespace Caloriary\Application\Action;

use BrandEmbassy\Slim\ActionHandler;
use BrandEmbassy\Slim\Request\RequestInterface;
use BrandEmbassy\Slim\Response\ResponseInterface;
use Caloriary\Application\Filtering\Exception\InvalidFilterQuery;
use Caloriary\Authentication\ReadModel\CountUsers;
use Caloriary\Authentication\ReadModel\GetListOfUsers;
use Caloriary\Authentication\Repository\Users;
use Caloriary\Authentication\Value\EmailAddress;
use Caloriary\Authorization\ACL\CanUserPerformAction;
use Caloriary\Authorization\Exception\RestrictedAccess;
use Caloriary\Authorization\Value\UserAction;
use Caloriary\Infrastructure\Application\Filtering\QueryFiltersFromRequestFactory;
use Caloriary\Infrastructure\Application\Pagination\PaginatorFromRequestFactory;
use Caloriary\Infrastructure\Application\Response\PaginatorResponseTransformer;
use Caloriary\Infrastructure\Application\Response\ResponseFormatter;
use Caloriary\Infrastructure\Application\Response\UserResponseTransformer;

final class ListUsersAction implements ActionHandler
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
	 * @var GetListOfUsers
	 */
	private $getListOfUsers;

	/**
	 * @var PaginatorFromRequestFactory
	 */
	private $paginatorFromRequestFactory;

	/**
	 * @var CountUsers
	 */
	private $countUsers;

	/**
	 * @var QueryFiltersFromRequestFactory
	 */
	private $queryFiltersFromRequestFactory;

	/**
	 * @var UserResponseTransformer
	 */
	private $userResponseTransformer;

	/**
	 * @var PaginatorResponseTransformer
	 */
	private $paginatorResponseTransformer;


	public function __construct(
		ResponseFormatter $responseFormatter,
		Users $users,
		GetListOfUsers $getListOfUsers,
		CanUserPerformAction $canUserPerformAction,
		PaginatorFromRequestFactory $paginatorFromRequestFactory,
		CountUsers $countUsers,
		QueryFiltersFromRequestFactory $queryFiltersFromRequestFactory,
		UserResponseTransformer $userResponseTransformer,
		PaginatorResponseTransformer $paginatorResponseTransformer
	)
	{
		$this->responseFormatter = $responseFormatter;
		$this->users = $users;
		$this->canUserPerformAction = $canUserPerformAction;
		$this->getListOfUsers = $getListOfUsers;
		$this->paginatorFromRequestFactory = $paginatorFromRequestFactory;
		$this->countUsers = $countUsers;
		$this->queryFiltersFromRequestFactory = $queryFiltersFromRequestFactory;
		$this->userResponseTransformer = $userResponseTransformer;
		$this->paginatorResponseTransformer = $paginatorResponseTransformer;
	}


	public function __invoke(RequestInterface $request, ResponseInterface $response, array $arguments = []): ResponseInterface
	{
		try {
			// @TODO: get user from attributes (set it via middleware)
			$currentUser = $this->users->get(
				EmailAddress::fromString($request->getAttribute('token')['sub'])
			);
			$action = UserAction::get(UserAction::LIST_USERS);

			if (! $this->canUserPerformAction->__invoke($currentUser, $action)) {
				throw new RestrictedAccess();
			}

			$queryFilters = $this->queryFiltersFromRequestFactory->create($request);
			$totalUsers = $this->countUsers->__invoke($queryFilters);
			$paginator = $this->paginatorFromRequestFactory->create($request, $totalUsers);
			$users = $this->getListOfUsers->__invoke($paginator, $queryFilters);
		}

		catch (InvalidFilterQuery $e) {
			return $this->responseFormatter->formatError($response, $e->getMessage(), 400);
		}

		catch (\InvalidArgumentException $e) {
			return $this->responseFormatter->formatError($response, $e->getMessage(), 400);
		}

		catch (RestrictedAccess $e) {
			return $this->responseFormatter->formatError($response, 'Not allowed', 403);
		}

		return $response->withJson(
			$this->paginatorResponseTransformer->toArray($paginator) + [
				'results' => array_map([$this->userResponseTransformer, 'toArray'], $users)
			],
		200
		);
	}
}
