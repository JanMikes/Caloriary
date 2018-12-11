<?php declare (strict_types=1);

namespace Caloriary\Application\Action;

use BrandEmbassy\Slim\ActionHandler;
use BrandEmbassy\Slim\Request\RequestInterface;
use BrandEmbassy\Slim\Response\ResponseInterface;
use Caloriary\Authentication\ReadModel\GetListOfUsers;
use Caloriary\Authentication\Repository\Users;
use Caloriary\Authentication\User;
use Caloriary\Authentication\Value\EmailAddress;
use Caloriary\Authorization\ACL\CanUserPerformAction;
use Caloriary\Authorization\Exception\RestrictedAccess;
use Caloriary\Authorization\Value\UserAction;
use Caloriary\Infrastructure\Application\Response\ResponseFormatter;
use Caloriary\PaginationInterface;
use Nette\Utils\Paginator;

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


	public function __construct(
		ResponseFormatter $responseFormatter,
		Users $users,
		GetListOfUsers $getListOfUsers,
		CanUserPerformAction $canUserPerformAction
	)
	{
		$this->responseFormatter = $responseFormatter;
		$this->users = $users;
		$this->canUserPerformAction = $canUserPerformAction;
		$this->getListOfUsers = $getListOfUsers;
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

			$paginator = $this->createPaginatorFromRequest($request);

			if ($this->getListOfUsers instanceof PaginationInterface) {
				$this->getListOfUsers->applyPaginator($paginator);
			}

			$users = $this->getListOfUsers->__invoke();
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


	private function createPaginatorFromRequest(RequestInterface $request): Paginator
	{
		// @TODO throw exception when page is out of scope || is not numeric
		$page = $request->getQueryParam('page', 1);

		// @TODO throw exception when limit is out of scope || is not numeric
		$itemsPerPage = $request->getQueryParam('limit', 3);

		$paginator = new Paginator();

		$paginator->setPage($page);
		$paginator->setItemsPerPage($itemsPerPage);
		$paginator->setItemCount(5);

		return $paginator;
	}
}
