<?php declare (strict_types=1);

namespace Caloriary\Application\Action;

use BrandEmbassy\Slim\ActionHandler;
use BrandEmbassy\Slim\Request\RequestInterface;
use BrandEmbassy\Slim\Response\ResponseInterface;
use Caloriary\Authentication\Repository\Users;
use Caloriary\Authentication\Value\EmailAddress;
use Caloriary\Authorization\ACL\CanUserPerformActionOnResource;
use Caloriary\Authorization\Exception\RestrictedAccess;
use Caloriary\Calories\Exception\CaloricRecordNotFound;
use Caloriary\Calories\Repository\CaloricRecords;
use Caloriary\Calories\Value\CaloricRecordId;
use Caloriary\Calories\Value\Calories;
use Caloriary\Calories\Value\MealDescription;
use Caloriary\Infrastructure\Application\Response\ResponseFormatter;
use Doctrine\Common\Persistence\ObjectManager;

final class EditEntryAction implements ActionHandler
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
	 * @var ObjectManager
	 */
	private $manager;


	public function __construct(
		ResponseFormatter $responseFormatter,
		Users $users,
		CaloricRecords $caloricRecords,
		CanUserPerformActionOnResource $canUserPerformActionOnResource,
		ObjectManager $manager
	)
	{
		$this->responseFormatter = $responseFormatter;
		$this->users = $users;
		$this->caloricRecords = $caloricRecords;
		$this->canUserPerformActionOnResource = $canUserPerformActionOnResource;
		$this->manager = $manager;
	}


	public function __invoke(RequestInterface $request, ResponseInterface $response, array $arguments = []): ResponseInterface
	{
		// @TODO: Validate body, via middleware?
		// @TODO: Transform into DTO, so we have strict types

		try {
			$body = $request->getDecodedJsonFromBody();
			// @TODO: get user from attributes (set it via middleware)
			$currentUser = $this->users->get(
				EmailAddress::fromString($request->getAttribute('token')['sub'])
			);
			$recordId = CaloricRecordId::fromString($arguments['entryId'] ?? '');
			$caloricRecord = $this->caloricRecords->get($recordId);

			$calories = Calories::fromInteger($body->calories ?? 0);
			$ateAt = \DateTimeImmutable::createFromFormat(DATE_ATOM, $body->date ?? '');
			// @TODO: if calories are not provided, then it should be calculated via API service
			$meal = MealDescription::fromString($body->text ?? '');

			if (! $ateAt instanceof \DateTimeImmutable) {
				throw new \InvalidArgumentException('Invalid date provided!');
			}

			$caloricRecord->edit(
				$calories,
				$ateAt,
				$meal,
				$currentUser,
				$this->canUserPerformActionOnResource
			);

			$this->manager->flush();
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
			'id' => $caloricRecord->id()->toString(),
			'date' => $caloricRecord->ateAt()->format(DATE_ATOM),
			'text' => $caloricRecord->text()->toString(),
			'calories' => $caloricRecord->calories()->toInteger(),
			'withinLimit' => true, // @TODO
		], 200);
	}
}
