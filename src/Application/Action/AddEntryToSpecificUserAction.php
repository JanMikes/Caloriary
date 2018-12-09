<?php declare (strict_types=1);

namespace Caloriary\Application\Action;

use BrandEmbassy\Slim\ActionHandler;
use BrandEmbassy\Slim\Request\RequestInterface;
use BrandEmbassy\Slim\Response\ResponseInterface;
use Caloriary\Authentication\Exception\UserNotFound;
use Caloriary\Authentication\Repository\Users;
use Caloriary\Authentication\Value\EmailAddress;
use Caloriary\Authorization\Exception\RestrictedAccess;
use Caloriary\Authorization\ACL\CanUserPerformAction;
use Caloriary\Authorization\Value\UserAction;
use Caloriary\Calories\CaloricRecord;
use Caloriary\Calories\Repository\CaloricRecords;
use Caloriary\Calories\Value\Calories;
use Caloriary\Calories\Value\MealDescription;
use Caloriary\Infrastructure\Application\Response\ResponseFormatter;

final class AddEntryToSpecificUserAction implements ActionHandler
{
	/**
	 * @var CaloricRecords
	 */
	private $caloricRecords;

	/**
	 * @var Users
	 */
	private $users;

	/**
	 * @var CanUserPerformAction
	 */
	private $canUserPerformAction;

	/**
	 * @var ResponseFormatter
	 */
	private $responseFormatter;


	public function __construct(
		CaloricRecords $caloricRecords,
		Users $users,
		CanUserPerformAction $canUserPerformAction,
		ResponseFormatter $responseFormatter
	)
	{
		$this->caloricRecords = $caloricRecords;
		$this->users = $users;
		$this->canUserPerformAction = $canUserPerformAction;
		$this->responseFormatter = $responseFormatter;
	}


	public function __invoke(RequestInterface $request, ResponseInterface $response, array $arguments = []): ResponseInterface
	{
		$body = $request->getDecodedJsonFromBody();

		try {
			// @TODO: get user from attributes (set it via middleware)
			$currentUser = $this->users->get(
				EmailAddress::fromString($request->getAttribute('token')['sub'])
			);
			$user = $this->users->get(
				EmailAddress::fromString($arguments['email'] ?? '')
			);

			$action = UserAction::get(UserAction::ADD_CALORIC_RECORD_TO_SPECIFIC_USER);

			if (! $this->canUserPerformAction->__invoke($currentUser, $action)) {
				throw new RestrictedAccess();
			}

			$calories = Calories::fromInteger($body->calories ?? 0);
			$ateAt = \DateTimeImmutable::createFromFormat(DATE_ATOM, $body->date ?? '');
			$meal = MealDescription::fromString($body->text ?? '');

			if (! $ateAt instanceof \DateTimeImmutable) {
				throw new \InvalidArgumentException('Invalid date provided!');
			}

			// @TODO: if calories are not provided, then it should be calculated via API service

			$record = CaloricRecord::create(
				$this->caloricRecords->nextIdentity(),
				$user,
				$calories,
				$ateAt,
				$meal,
				$this->canUserPerformAction
			);
		}

		catch (\InvalidArgumentException $e) {
			return $this->responseFormatter->formatError($response, $e->getMessage());
		}

		catch (UserNotFound $e) {
			return $this->responseFormatter->formatError($response, 'User not found!', 404);
		}

		catch (RestrictedAccess $e) {
			return $this->responseFormatter->formatError($response, 'Not allowed', 403);
		}

		$this->caloricRecords->add($record);

		// @TODO: transformer for response
		return $response->withJson([
			'id' => $record->id()->toString(),
			'date' => $record->ateAt()->format(DATE_ATOM),
			'calories' => $record->calories()->toInteger(),
			'text' => $record->text()->toString(),
			'withinLimit' => true, // @TODO
		], 201);
	}
}
