<?php declare (strict_types=1);

namespace Caloriary\Application\Action;

use BrandEmbassy\Slim\ActionHandler;
use BrandEmbassy\Slim\Request\RequestInterface;
use BrandEmbassy\Slim\Response\ResponseInterface;
use Caloriary\Authentication\Exception\UserNotFound;
use Caloriary\Authentication\Repository\Users;
use Caloriary\Authentication\User;
use Caloriary\Authentication\Value\EmailAddress;
use Caloriary\Authorization\Exception\RestrictedAccess;
use Caloriary\Authorization\ACL\CanUserPerformAction;
use Caloriary\Authorization\Value\UserAction;
use Caloriary\Calories\CaloricRecord;
use Caloriary\Calories\Exception\MealNotFound;
use Caloriary\Calories\ReadModel\GetCaloriesForMeal;
use Caloriary\Calories\Repository\CaloricRecords;
use Caloriary\Calories\Value\Calories;
use Caloriary\Calories\Value\MealDescription;
use Caloriary\Infrastructure\Application\Response\CaloricRecordResponseTransformer;
use Caloriary\Infrastructure\Application\Response\ResponseFormatter;

final class AddCaloricRecordToSpecificUserAction implements ActionHandler
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

	/**
	 * @var GetCaloriesForMeal
	 */
	private $getCaloriesForMeal;

	/**
	 * @var CaloricRecordResponseTransformer
	 */
	private $caloricRecordResponseTransformer;


	public function __construct(
		CaloricRecords $caloricRecords,
		Users $users,
		CanUserPerformAction $canUserPerformAction,
		ResponseFormatter $responseFormatter,
		GetCaloriesForMeal $getCaloriesForMeal,
		CaloricRecordResponseTransformer $caloricRecordResponseTransformer
	)
	{
		$this->caloricRecords = $caloricRecords;
		$this->users = $users;
		$this->canUserPerformAction = $canUserPerformAction;
		$this->responseFormatter = $responseFormatter;
		$this->getCaloriesForMeal = $getCaloriesForMeal;
		$this->caloricRecordResponseTransformer = $caloricRecordResponseTransformer;
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

			$this->ensureUserCanAddCaloricRecordToAnotherUser($currentUser);

			$ateAt = \DateTimeImmutable::createFromFormat('Y-m-d H:i', $body->date . ' ' . $body->time);

			if (! $ateAt instanceof \DateTimeImmutable) {
				throw new \InvalidArgumentException('Invalid date provided!');
			}

			$meal = MealDescription::fromString($body->text ?? '');

			if (isset($body->calories)) {
				$calories = Calories::fromInteger($body->calories);
			} else {
				$calories = $this->getCaloriesForMeal->__invoke($meal);
			}

			$caloricRecord = CaloricRecord::create(
				$this->caloricRecords->nextIdentity(),
				$user,
				$calories,
				$ateAt,
				$meal,
				$this->canUserPerformAction
			);

			$this->caloricRecords->add($caloricRecord);

			return $response->withJson($this->caloricRecordResponseTransformer->toArray($caloricRecord), 201);
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

		catch (MealNotFound $e) {
			return $this->responseFormatter->formatError($response, $e->getMessage());
		}
	}


	/**
	 * @param User $currentUser
	 */
	private function ensureUserCanAddCaloricRecordToAnotherUser(User $currentUser): void
	{
		$action = UserAction::get(UserAction::ADD_CALORIC_RECORD_TO_SPECIFIC_USER);

		if (!$this->canUserPerformAction->__invoke($currentUser, $action)) {
			throw new RestrictedAccess();
		}
	}
}
