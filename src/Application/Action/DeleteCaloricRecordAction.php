<?php

declare(strict_types=1);

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
use Caloriary\Infrastructure\Application\Response\ResponseFormatter;
use Caloriary\Infrastructure\Authentication\UserProvider;

final class DeleteCaloricRecordAction implements ActionHandler
{
    /**
     * @var ResponseFormatter
     */
    private $responseFormatter;

    /**
     * @var CanUserPerformActionOnResource
     */
    private $canUserPerformActionOnResource;

    /**
     * @var CaloricRecords
     */
    private $caloricRecords;

    /**
     * @var UserProvider
     */
    private $userProvider;


    public function __construct(
        ResponseFormatter $responseFormatter,
        CaloricRecords $caloricRecords,
        CanUserPerformActionOnResource $canUserPerformActionOnResource,
        UserProvider $userProvider
    ) {
        $this->responseFormatter = $responseFormatter;
        $this->canUserPerformActionOnResource = $canUserPerformActionOnResource;
        $this->caloricRecords = $caloricRecords;
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
            $action = UserAction::get(UserAction::DELETE_CALORIC_RECORD);

            if (!$this->canUserPerformActionOnResource->__invoke($currentUser, $action, $caloricRecord)) {
                throw new RestrictedAccess();
            }

            $this->caloricRecords->remove($caloricRecord);

            return $response->withJson([
                'success' => true,
            ], 200);
        } catch (\InvalidArgumentException $e) {
            return $this->responseFormatter->formatError($response, $e->getMessage());
        } catch (CaloricRecordNotFound $e) {
            return $this->responseFormatter->formatError($response, 'Caloric record not found!', 404);
        } catch (RestrictedAccess $e) {
            return $this->responseFormatter->formatError($response, 'Not allowed', 403);
        }
    }
}
