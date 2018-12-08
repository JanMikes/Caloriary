<?php declare (strict_types=1);

namespace Caloriary\Application\Action;

use BrandEmbassy\Slim\ActionHandler;
use BrandEmbassy\Slim\Request\RequestInterface;
use BrandEmbassy\Slim\Response\ResponseInterface;
use Caloriary\Authentication\Repository\Users;
use Caloriary\Authentication\Value\EmailAddress;
use Caloriary\Authorization\Exception\RestrictedAccess;
use Caloriary\Authorization\ReadModel\CanUserPerformAction;
use Caloriary\Calories\CaloricRecord;
use Caloriary\Calories\Repository\CaloricRecords;
use Caloriary\Calories\Value\Calories;
use Caloriary\Calories\Value\MealDescription;
use Caloriary\Infrastructure\Application\Response\ResponseFormatter;

final class AddEntryAction implements ActionHandler
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
			$email = EmailAddress::fromString($request->getAttribute('token')['sub']);
			$calories = Calories::fromInteger($body->calories ?? 0);
			$ateAt = \DateTimeImmutable::createFromFormat($body->date ?? '', DATE_ATOM);
			$meal = MealDescription::fromString($body->text ?? '');

			// @TODO: if calories are not provided, then it should be calculated via API service

			$record = CaloricRecord::create(
				$this->caloricRecords->nextIdentity(),
				$this->users->get($email),
				$calories,
				$ateAt,
				$meal,
				$this->canUserPerformAction
			);
		}

		catch (\InvalidArgumentException $e) {
			return $this->responseFormatter->formatError($response, $e->getMessage());
		}

		catch (RestrictedAccess $e) {
			return $this->responseFormatter->formatError($response, 'Not allowed', 403);
		}

		$this->caloricRecords->add($record);

		return $response->withJson([
			'id' => $record->id()->toString(),
			'date' => $record->ateAt()->format(DATE_ATOM),
			'calories' => $record->calories()->toInteger(),
			'text' => $record->text()->toString(),
			'withinLimit' => true, // @TODO
		], 201);
	}
}
