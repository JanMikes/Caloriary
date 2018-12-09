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
use Caloriary\Infrastructure\Application\Response\ResponseFormatter;

final class DeleteEntryAction implements ActionHandler
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
	 * @var CanUserPerformActionOnResource
	 */
	private $canUserPerformActionOnResource;

	/**
	 * @var CaloricRecords
	 */
	private $caloricRecords;


	public function __construct(
		ResponseFormatter $responseFormatter,
		Users $users,
		CaloricRecords $caloricRecords,
		CanUserPerformActionOnResource $canUserPerformActionOnResource
	)
	{
		$this->responseFormatter = $responseFormatter;
		$this->users = $users;
		$this->canUserPerformActionOnResource = $canUserPerformActionOnResource;
		$this->caloricRecords = $caloricRecords;
	}


	public function __invoke(RequestInterface $request, ResponseInterface $response, array $arguments = []): ResponseInterface
	{
		try {
			// @TODO: get user from attributes (set it via middleware)
			$currentUser = $this->users->get(
				EmailAddress::fromString($request->getAttribute('token')['sub'])
			);
			$recordId = CaloricRecordId::fromString($arguments['entryId'] ?? '');
			$caloricRecord = $this->caloricRecords->get($recordId);
			$action = UserAction::get(UserAction::DELETE_CALORIC_RECORD);

			if (! $this->canUserPerformActionOnResource->__invoke($currentUser, $action, $caloricRecord)) {
				throw new RestrictedAccess();
			}

			$this->caloricRecords->remove($caloricRecord);
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

		// @TODO: transformer for response
		return $response->withJson([
			'success' => true,
		], 200);
	}
}
