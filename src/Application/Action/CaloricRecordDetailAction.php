<?php declare (strict_types=1);

namespace Caloriary\Application\Action;

use BrandEmbassy\Slim\ActionHandler;
use BrandEmbassy\Slim\Request\RequestInterface;
use BrandEmbassy\Slim\Response\ResponseInterface;
use Caloriary\Authentication\Repository\Users;
use Caloriary\Authentication\Value\EmailAddress;
use Caloriary\Authorization\ACL\CanUserPerformActionOnResource;
use Caloriary\Authorization\Exception\RestrictedAccess;
use Caloriary\Authorization\Value\UserAction;
use Caloriary\Calories\Exception\CaloricRecordNotFound;
use Caloriary\Calories\Repository\CaloricRecords;
use Caloriary\Calories\Value\CaloricRecordId;
use Caloriary\Infrastructure\Application\Response\CaloricRecordResponseTransformer;
use Caloriary\Infrastructure\Application\Response\ResponseFormatter;

final class CaloricRecordDetailAction implements ActionHandler
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


	public function __construct(
		ResponseFormatter $responseFormatter,
		Users $users,
		CaloricRecords $caloricRecords,
		CanUserPerformActionOnResource $canUserPerformActionOnResource,
		CaloricRecordResponseTransformer $caloricRecordResponseTransformer
	)
	{
		$this->responseFormatter = $responseFormatter;
		$this->users = $users;
		$this->caloricRecords = $caloricRecords;
		$this->canUserPerformActionOnResource = $canUserPerformActionOnResource;
		$this->caloricRecordResponseTransformer = $caloricRecordResponseTransformer;
	}


	public function __invoke(RequestInterface $request, ResponseInterface $response, array $arguments = []): ResponseInterface
	{
		try {
			// @TODO: get user from attributes (set it via middleware)
			$currentUser = $this->users->get(
				EmailAddress::fromString($request->getAttribute('token')['sub'])
			);
			$recordId = CaloricRecordId::fromString($arguments['caloricRecordId'] ?? '');
			$caloricRecord = $this->caloricRecords->get($recordId);
			$action = UserAction::get(UserAction::CALORIC_RECORD_DETAIL);

			if (! $this->canUserPerformActionOnResource->__invoke($currentUser, $action, $caloricRecord)) {
				throw new RestrictedAccess();
			}
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

		return $response->withJson($this->caloricRecordResponseTransformer->toArray($caloricRecord), 200);
	}
}