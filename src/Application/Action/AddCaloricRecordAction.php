<?php declare (strict_types=1);

namespace Caloriary\Application\Action;

use BrandEmbassy\Slim\ActionHandler;
use BrandEmbassy\Slim\Request\RequestInterface;
use BrandEmbassy\Slim\Response\ResponseInterface;
use Caloriary\Authorization\ACL\CanUserPerformAction;
use Caloriary\Authorization\Exception\RestrictedAccess;
use Caloriary\Calories\CaloricRecord;
use Caloriary\Calories\Exception\MealNotFound;
use Caloriary\Calories\ReadModel\GetCaloriesForMeal;
use Caloriary\Calories\Repository\CaloricRecords;
use Caloriary\Calories\Value\Calories;
use Caloriary\Calories\Value\MealDescription;
use Caloriary\Infrastructure\Application\Response\CaloricRecordResponseTransformer;
use Caloriary\Infrastructure\Application\Response\ResponseFormatter;
use Caloriary\Infrastructure\Authentication\UserProvider;

final class AddCaloricRecordAction implements ActionHandler
{
	/**
	 * @var CaloricRecords
	 */
	private $caloricRecords;

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

	/**
	 * @var UserProvider
	 */
	private $userProvider;


	public function __construct(
		CaloricRecords $caloricRecords,
		CanUserPerformAction $canUserPerformAction,
		ResponseFormatter $responseFormatter,
		GetCaloriesForMeal $getCaloriesForMeal,
		CaloricRecordResponseTransformer $caloricRecordResponseTransformer,
		UserProvider $userProvider
	)
	{
		$this->caloricRecords = $caloricRecords;
		$this->canUserPerformAction = $canUserPerformAction;
		$this->responseFormatter = $responseFormatter;
		$this->getCaloriesForMeal = $getCaloriesForMeal;
		$this->caloricRecordResponseTransformer = $caloricRecordResponseTransformer;
		$this->userProvider = $userProvider;
	}


	public function __invoke(RequestInterface $request, ResponseInterface $response, array $arguments = []): ResponseInterface
	{
		$body = $request->getDecodedJsonFromBody();

		try {
			$currentUser = $this->userProvider->currentUser();
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
				$currentUser,
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

		catch (RestrictedAccess $e) {
			return $this->responseFormatter->formatError($response, 'Not allowed', 403);
		}

		catch (MealNotFound $e) {
			return $this->responseFormatter->formatError($response, $e->getMessage());
		}
	}
}
