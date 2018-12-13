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
use Caloriary\Calories\ReadModel\CountCaloricRecordsOfUser;
use Caloriary\Calories\ReadModel\GetListOfCaloricRecordsForUser;
use Caloriary\Infrastructure\Application\Filtering\QueryFiltersFromRequestFactory;
use Caloriary\Infrastructure\Application\Pagination\PaginatorFromRequestFactory;
use Caloriary\Infrastructure\Application\Response\CaloricRecordResponseTransformer;
use Caloriary\Infrastructure\Application\Response\PaginatorResponseTransformer;
use Caloriary\Infrastructure\Application\Response\ResponseFormatter;
use Caloriary\Infrastructure\Authentication\UserProvider;

final class ListCaloricRecordsForSpecificUserAction implements ActionHandler
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
	 * @var PaginatorFromRequestFactory
	 */
	private $paginatorFromRequestFactory;

	/**
	 * @var CountCaloricRecordsOfUser
	 */
	private $countCaloricRecordsOfUser;

	/**
	 * @var QueryFiltersFromRequestFactory
	 */
	private $queryFiltersFromRequestFactory;

	/**
	 * @var PaginatorResponseTransformer
	 */
	private $paginatorResponseTransformer;

	/**
	 * @var CaloricRecordResponseTransformer
	 */
	private $caloricRecordResponseTransformer;

	/**
	 * @var UserProvider
	 */
	private $userProvider;


	public function __construct(
		ResponseFormatter $responseFormatter,
		Users $users,
		GetListOfCaloricRecordsForUser $getListOfCaloricRecordsForUser,
		CanUserPerformAction $canUserPerformAction,
		PaginatorFromRequestFactory $paginatorFromRequestFactory,
		CountCaloricRecordsOfUser $countCaloricRecordsOfUser,
		QueryFiltersFromRequestFactory $queryFiltersFromRequestFactory,
		PaginatorResponseTransformer $paginatorResponseTransformer,
		CaloricRecordResponseTransformer $caloricRecordResponseTransformer,
		UserProvider $userProvider
	)
	{
		$this->responseFormatter = $responseFormatter;
		$this->users = $users;
		$this->canUserPerformAction = $canUserPerformAction;
		$this->getListOfCaloricRecordsForUser = $getListOfCaloricRecordsForUser;
		$this->paginatorFromRequestFactory = $paginatorFromRequestFactory;
		$this->countCaloricRecordsOfUser = $countCaloricRecordsOfUser;
		$this->queryFiltersFromRequestFactory = $queryFiltersFromRequestFactory;
		$this->paginatorResponseTransformer = $paginatorResponseTransformer;
		$this->caloricRecordResponseTransformer = $caloricRecordResponseTransformer;
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
			$action = UserAction::get(UserAction::LIST_CALORIC_RECORDS_FOR_SPECIFIC_USER);

			if (! $this->canUserPerformAction->__invoke($currentUser, $action)) {
				throw new RestrictedAccess();
			}

			$queryFilters = $this->queryFiltersFromRequestFactory->create($request);
			$totalCaloricRecordsCount = $this->countCaloricRecordsOfUser->__invoke($user, $queryFilters);
			$paginator = $this->paginatorFromRequestFactory->create($request, $totalCaloricRecordsCount);
			$caloricRecords = $this->getListOfCaloricRecordsForUser->__invoke($user, $paginator, $queryFilters);

			return $response->withJson(
				$this->paginatorResponseTransformer->toArray($paginator) + [
					'results' => array_map([$this->caloricRecordResponseTransformer, 'toArray'], $caloricRecords)
				],
				200
			);
		}

		catch (\InvalidArgumentException $e) {
			return $this->responseFormatter->formatError($response, $e->getMessage(), 400);
		}

		catch (UserNotFound $e) {
			return $this->responseFormatter->formatError($response, 'User not found!', 404);
		}

		catch (RestrictedAccess $e) {
			return $this->responseFormatter->formatError($response, 'Not allowed', 403);
		}
	}
}
