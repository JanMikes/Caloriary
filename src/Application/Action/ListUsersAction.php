<?php declare (strict_types=1);

namespace Caloriary\Application\Action;

use BrandEmbassy\Slim\ActionHandler;
use BrandEmbassy\Slim\Request\RequestInterface;
use BrandEmbassy\Slim\Response\ResponseInterface;
use Caloriary\Application\Filtering\Exception\InvalidFilterQuery;
use Caloriary\Authentication\ReadModel\CountUsers;
use Caloriary\Authentication\ReadModel\GetListOfUsers;
use Caloriary\Authorization\ACL\CanUserPerformAction;
use Caloriary\Authorization\Exception\RestrictedAccess;
use Caloriary\Authorization\Value\UserAction;
use Caloriary\Infrastructure\Application\Filtering\QueryFiltersFromRequestFactory;
use Caloriary\Infrastructure\Application\Pagination\PaginatorFromRequestFactory;
use Caloriary\Infrastructure\Application\Response\PaginatorResponseTransformer;
use Caloriary\Infrastructure\Application\Response\ResponseFormatter;
use Caloriary\Infrastructure\Application\Response\UserResponseTransformer;
use Caloriary\Infrastructure\Authentication\UserProvider;

final class ListUsersAction implements ActionHandler
{
	/**
	 * @var ResponseFormatter
	 */
	private $responseFormatter;

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

	/**
	 * @var UserProvider
	 */
	private $userProvider;


	public function __construct(
		ResponseFormatter $responseFormatter,
		GetListOfUsers $getListOfUsers,
		CanUserPerformAction $canUserPerformAction,
		PaginatorFromRequestFactory $paginatorFromRequestFactory,
		CountUsers $countUsers,
		QueryFiltersFromRequestFactory $queryFiltersFromRequestFactory,
		UserResponseTransformer $userResponseTransformer,
		PaginatorResponseTransformer $paginatorResponseTransformer,
		UserProvider $userProvider
	)
	{
		$this->responseFormatter = $responseFormatter;
		$this->canUserPerformAction = $canUserPerformAction;
		$this->getListOfUsers = $getListOfUsers;
		$this->paginatorFromRequestFactory = $paginatorFromRequestFactory;
		$this->countUsers = $countUsers;
		$this->queryFiltersFromRequestFactory = $queryFiltersFromRequestFactory;
		$this->userResponseTransformer = $userResponseTransformer;
		$this->paginatorResponseTransformer = $paginatorResponseTransformer;
		$this->userProvider = $userProvider;
	}


	/**
	 * @param string[] $arguments
	 */
	public function __invoke(RequestInterface $request, ResponseInterface $response, array $arguments = []): ResponseInterface
	{
		try {
			$currentUser = $this->userProvider->currentUser();
			$action = UserAction::get(UserAction::LIST_USERS);

			if (! $this->canUserPerformAction->__invoke($currentUser, $action)) {
				throw new RestrictedAccess();
			}

			$queryFilters = $this->queryFiltersFromRequestFactory->create($request);
			$totalUsers = $this->countUsers->__invoke($queryFilters);
			$paginator = $this->paginatorFromRequestFactory->create($request, $totalUsers);
			$users = $this->getListOfUsers->__invoke($paginator, $queryFilters);

			return $response->withJson(
				$this->paginatorResponseTransformer->toArray($paginator) + [
					'results' => array_map([$this->userResponseTransformer, 'toArray'], $users)
				],
			200
			);
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
	}
}
