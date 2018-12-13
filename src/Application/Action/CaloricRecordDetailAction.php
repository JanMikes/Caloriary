<?php declare (strict_types=1);

namespace Caloriary\Application\Action;

use BrandEmbassy\Slim\ActionHandler;
use BrandEmbassy\Slim\Request\RequestInterface;
use BrandEmbassy\Slim\Response\ResponseInterface;
use Caloriary\Authorization\ACL\CanUserPerformActionOnResource;
use Caloriary\Authorization\Exception\RestrictedAccess;
use Caloriary\Authorization\Value\UserAction;
use Caloriary\Calories\Exception\CaloricRecordNotFound;
use Caloriary\Calories\Repository\CaloricRecords;
use Caloriary\Calories\Value\CaloricRecordId;
use Caloriary\Infrastructure\Application\Response\CaloricRecordResponseTransformer;
use Caloriary\Infrastructure\Application\Response\ResponseFormatter;
use Caloriary\Infrastructure\Authentication\UserProvider;

final class CaloricRecordDetailAction implements ActionHandler
{
	/**
	 * @var ResponseFormatter
	 */
	private $responseFormatter;

	/**
	 * @var CaloricRecords
	 */
	private $caloricRecords;

	/**
	 * @var CanUserPerformActionOnResource
	 */
	private $canUserPerformActionOnResource;

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
		CaloricRecords $caloricRecords,
		CanUserPerformActionOnResource $canUserPerformActionOnResource,
		CaloricRecordResponseTransformer $caloricRecordResponseTransformer,
		UserProvider $userProvider
	)
	{
		$this->responseFormatter = $responseFormatter;
		$this->caloricRecords = $caloricRecords;
		$this->canUserPerformActionOnResource = $canUserPerformActionOnResource;
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
			$recordId = CaloricRecordId::fromString($arguments['caloricRecordId'] ?? '');
			$caloricRecord = $this->caloricRecords->get($recordId);
			$action = UserAction::get(UserAction::CALORIC_RECORD_DETAIL);

			if (! $this->canUserPerformActionOnResource->__invoke($currentUser, $action, $caloricRecord)) {
				throw new RestrictedAccess();
			}

			return $response->withJson($this->caloricRecordResponseTransformer->toArray($caloricRecord), 200);
		}

		catch (\InvalidArgumentException $e) {
			return $this->responseFormatter->formatError($response, $e->getMessage());
		}

		catch (CaloricRecordNotFound $e) {
			return $this->responseFormatter->formatError($response, 'Caloric record not found!', 404);
		}

		catch (RestrictedAccess $e) {
			return $this->responseFormatter->formatError($response, 'Not allowed', 403);
		}
	}
}
