<?php declare (strict_types=1);

namespace Caloriary\Application\Action;

use BrandEmbassy\Slim\ActionHandler;
use BrandEmbassy\Slim\Request\RequestInterface;
use BrandEmbassy\Slim\Response\ResponseInterface;
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

final class ListCaloricRecordsAction implements ActionHandler
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

	/**
	 * @var CaloricRecordResponseTransformer
	 */
	private $caloricRecordResponseTransformer;

	/**
	 * @var PaginatorResponseTransformer
	 */
	private $paginatorResponseTransformer;


	public function __construct(
		ResponseFormatter $responseFormatter,
		Users $users,
		GetListOfCaloricRecordsForUser $getListOfCaloricRecordsForUser,
		CanUserPerformAction $canUserPerformAction,
		CountCaloricRecordsOfUser $countCaloricRecordsOfUser,
		PaginatorFromRequestFactory $paginatorFromRequestFactory,
		QueryFiltersFromRequestFactory $queryFiltersFromRequestFactory,
		CaloricRecordResponseTransformer $caloricRecordResponseTransformer,
		PaginatorResponseTransformer $paginatorResponseTransformer
	)
	{
		$this->responseFormatter = $responseFormatter;
		$this->users = $users;
		$this->canUserPerformAction = $canUserPerformAction;
		$this->getListOfCaloricRecordsForUser = $getListOfCaloricRecordsForUser;
		$this->countCaloricRecordsOfUser = $countCaloricRecordsOfUser;
		$this->paginatorFromRequestFactory = $paginatorFromRequestFactory;
		$this->queryFiltersFromRequestFactory = $queryFiltersFromRequestFactory;
		$this->caloricRecordResponseTransformer = $caloricRecordResponseTransformer;
		$this->paginatorResponseTransformer = $paginatorResponseTransformer;
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
			$totalCaloricRecordsCount = $this->countCaloricRecordsOfUser->__invoke($currentUser, $queryFilters);
			$paginator = $this->paginatorFromRequestFactory->create($request, $totalCaloricRecordsCount);
			$caloricRecords = $this->getListOfCaloricRecordsForUser->__invoke($currentUser, $paginator, $queryFilters);

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

		catch (RestrictedAccess $e) {
			return $this->responseFormatter->formatError($response, 'Not allowed', 403);
		}
	}
}
