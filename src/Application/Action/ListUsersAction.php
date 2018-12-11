<?php declare (strict_types=1);

namespace Caloriary\Application\Action;

use BrandEmbassy\Slim\ActionHandler;
use BrandEmbassy\Slim\Request\RequestInterface;
use BrandEmbassy\Slim\Response\ResponseInterface;
use Caloriary\Authentication\ReadModel\CountUsers;
use Caloriary\Authentication\ReadModel\GetListOfUsers;
use Caloriary\Authentication\Repository\Users;
use Caloriary\Authentication\User;
use Caloriary\Authentication\Value\EmailAddress;
use Caloriary\Authorization\ACL\CanUserPerformAction;
use Caloriary\Authorization\Exception\RestrictedAccess;
use Caloriary\Authorization\Value\UserAction;
use Caloriary\Infrastructure\Application\Pagination\PaginatorFromRequestFactory;
use Caloriary\Infrastructure\Application\Response\ResponseFormatter;
use Caloriary\Application\Pagination\PaginationAwareQuery;

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


	public function __construct(
		ResponseFormatter $responseFormatter,
		Users $users,
		GetListOfUsers $getListOfUsers,
		CanUserPerformAction $canUserPerformAction,
		PaginatorFromRequestFactory $paginatorFromRequestFactory,
		CountUsers $countUsers
	)
	{
		$this->responseFormatter = $responseFormatter;
		$this->users = $users;
		$this->canUserPerformAction = $canUserPerformAction;
		$this->getListOfUsers = $getListOfUsers;
		$this->paginatorFromRequestFactory = $paginatorFromRequestFactory;
		$this->countUsers = $countUsers;
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

			$paginator = $this->paginatorFromRequestFactory->create($request, $this->countUsers->__invoke());

			if ($this->getListOfUsers instanceof PaginationAwareQuery) {
				$this->getListOfUsers->applyPaginator($paginator);
			}

			$users = $this->getListOfUsers->__invoke();
		}

		catch (\InvalidArgumentException $e) {
			return $this->responseFormatter->formatError($response, $e->getMessage(), 400);
		}

		catch (RestrictedAccess $e) {
			return $this->responseFormatter->formatError($response, 'Not allowed', 403);
		}

		// @TODO: transformer for response
		return $response->withJson([
			'page' => $paginator->getPage(),
			'limit' => $paginator->getItemsPerPage(),
			'pages' => $paginator->getPageCount(),
			'totalCount' => $paginator->getItemCount(),
			'results' => array_map(function(User $user) {
				return [
					'email' => $user->emailAddress()->toString(),
					'dailyLimit' => $user->dailyLimit()->toInteger(),
				];
			}, $users)
		], 200);
	}
}
