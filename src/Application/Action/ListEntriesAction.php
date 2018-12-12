<?php declare (strict_types=1);

namespace Caloriary\Application\Action;

use BrandEmbassy\Slim\ActionHandler;
use BrandEmbassy\Slim\Request\RequestInterface;
use BrandEmbassy\Slim\Response\ResponseInterface;
use Caloriary\Application\Filtering\FilteringAwareQuery;
use Caloriary\Application\Pagination\PaginationAwareQuery;
use Caloriary\Authentication\Repository\Users;
use Caloriary\Authentication\Value\EmailAddress;
use Caloriary\Authorization\ACL\CanUserPerformAction;
use Caloriary\Authorization\Exception\RestrictedAccess;
use Caloriary\Authorization\Value\UserAction;
use Caloriary\Calories\CaloricRecord;
use Caloriary\Calories\ReadModel\CountCaloricRecordsOfUser;
use Caloriary\Calories\ReadModel\GetListOfCaloricRecordsForUser;
use Caloriary\Calories\ReadModel\HasCaloriesWithinDailyLimit;
use Caloriary\Infrastructure\Application\Filtering\QueryFiltersFromRequestFactory;
use Caloriary\Infrastructure\Application\Pagination\PaginatorFromRequestFactory;
use Caloriary\Infrastructure\Application\Response\ResponseFormatter;

final class ListEntriesAction implements ActionHandler
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
	 * @var GetListOfCaloricRecordsForUser
	 */
	private $getListOfCaloricRecordsForUser;

	/**
	 * @var HasCaloriesWithinDailyLimit
	 */
	private $hasCaloriesWithinDailyLimit;

	/**
	 * @var CountCaloricRecordsOfUser
	 */
	private $countCaloricRecordsOfUser;

	/**
	 * @var PaginatorFromRequestFactory
	 */
	private $paginatorFromRequestFactory;

	/**
	 * @var QueryFiltersFromRequestFactory
	 */
	private $queryFiltersFromRequestFactory;


	public function __construct(
		ResponseFormatter $responseFormatter,
		Users $users,
		GetListOfCaloricRecordsForUser $getListOfCaloricRecordsForUser,
		CanUserPerformAction $canUserPerformAction,
		HasCaloriesWithinDailyLimit $hasCaloriesWithinDailyLimit,
		CountCaloricRecordsOfUser $countCaloricRecordsOfUser,
		PaginatorFromRequestFactory $paginatorFromRequestFactory,
		QueryFiltersFromRequestFactory $queryFiltersFromRequestFactory
	)
	{
		$this->responseFormatter = $responseFormatter;
		$this->users = $users;
		$this->canUserPerformAction = $canUserPerformAction;
		$this->getListOfCaloricRecordsForUser = $getListOfCaloricRecordsForUser;
		$this->hasCaloriesWithinDailyLimit = $hasCaloriesWithinDailyLimit;
		$this->countCaloricRecordsOfUser = $countCaloricRecordsOfUser;
		$this->paginatorFromRequestFactory = $paginatorFromRequestFactory;
		$this->queryFiltersFromRequestFactory = $queryFiltersFromRequestFactory;
	}


	public function __invoke(RequestInterface $request, ResponseInterface $response, array $arguments = []): ResponseInterface
	{
		try {
			// @TODO: get user from attributes (set it via middleware)
			$currentUser = $this->users->get(
				EmailAddress::fromString($request->getAttribute('token')['sub'])
			);
			$action = UserAction::get(UserAction::LIST_CALORIC_RECORDS);

			if (! $this->canUserPerformAction->__invoke($currentUser, $action)) {
				throw new RestrictedAccess();
			}

			$queryFilters = $this->queryFiltersFromRequestFactory->create($request);

			if ($this->countCaloricRecordsOfUser instanceof FilteringAwareQuery) {
				$this->countCaloricRecordsOfUser->applyFiltersForNextQuery($queryFilters);
			}

			$paginator = $this->paginatorFromRequestFactory->create(
				$request,
				$this->countCaloricRecordsOfUser->__invoke($currentUser)
			);

			if ($this->getListOfCaloricRecordsForUser instanceof PaginationAwareQuery) {
				$this->getListOfCaloricRecordsForUser->applyPaginatorForNextQuery($paginator);
			}

			if ($this->getListOfCaloricRecordsForUser instanceof FilteringAwareQuery) {
				$this->getListOfCaloricRecordsForUser->applyFiltersForNextQuery($queryFilters);
			}

			$records = $this->getListOfCaloricRecordsForUser->__invoke($currentUser);
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
			'results' => array_map(function(CaloricRecord $record) {
				return [
					'id' => $record->id()->toString(),
					'date' => $record->ateAt()->format('Y-m-d'),
					'time' => $record->ateAt()->format('H:i'),
					'text' => $record->text()->toString(),
					'calories' => $record->calories()->toInteger(),
					'withinLimit' => $this->hasCaloriesWithinDailyLimit->__invoke($record),
				];
			}, $records)
		], 200);
	}
}
