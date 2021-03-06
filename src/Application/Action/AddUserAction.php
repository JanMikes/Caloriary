<?php

declare(strict_types=1);

namespace Caloriary\Application\Action;

use BrandEmbassy\Slim\ActionHandler;
use BrandEmbassy\Slim\Request\RequestInterface;
use BrandEmbassy\Slim\Response\ResponseInterface;
use Caloriary\Authentication\Exception\EmailAddressAlreadyRegistered;
use Caloriary\Authentication\ReadModel\IsEmailRegistered;
use Caloriary\Authentication\Repository\Users;
use Caloriary\Authentication\User;
use Caloriary\Authentication\Value\ClearTextPassword;
use Caloriary\Authentication\Value\EmailAddress;
use Caloriary\Authorization\ACL\CanUserPerformAction;
use Caloriary\Authorization\Exception\RestrictedAccess;
use Caloriary\Calories\Value\DailyCaloriesLimit;
use Caloriary\Infrastructure\Application\Response\ResponseFormatter;
use Caloriary\Infrastructure\Application\Response\UserResponseTransformer;
use Caloriary\Infrastructure\Authentication\UserProvider;

final class AddUserAction implements ActionHandler
{
    /**
     * @var IsEmailRegistered
     */
    private $isEmailRegistered;

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
     * @var UserResponseTransformer
     */
    private $userResponseTransformer;

    /**
     * @var UserProvider
     */
    private $userProvider;


    public function __construct(
        ResponseFormatter $responseFormatter,
        IsEmailRegistered $isEmailRegistered,
        Users $users,
        CanUserPerformAction $canUserPerformAction,
        UserResponseTransformer $userResponseTransformer,
        UserProvider $userProvider
    ) {
        $this->responseFormatter = $responseFormatter;
        $this->isEmailRegistered = $isEmailRegistered;
        $this->users = $users;
        $this->canUserPerformAction = $canUserPerformAction;
        $this->userResponseTransformer = $userResponseTransformer;
        $this->userProvider = $userProvider;
    }


    /**
     * @param string[] $arguments
     */
    public function __invoke(RequestInterface $request, ResponseInterface $response, array $arguments = []): ResponseInterface
    {
        try {
            $body = $request->getDecodedJsonFromBody();
            $currentUser = $this->userProvider->currentUser();
            $emailAddress = EmailAddress::fromString($body->email ?? '');
            $password = ClearTextPassword::fromString($body->password ?? '');
            $dailyLimit = DailyCaloriesLimit::fromInteger($body->dailyLimit ?? 0);

            $user = User::createByAdmin(
                $emailAddress,
                $password,
                $dailyLimit,
                $currentUser,
                $this->isEmailRegistered,
                $this->canUserPerformAction
            );

            $this->users->add($user);

            return $response->withJson($this->userResponseTransformer->toArray($user), 201);
        } catch (\InvalidArgumentException $e) {
            return $this->responseFormatter->formatError($response, $e->getMessage());
        } catch (EmailAddressAlreadyRegistered $e) {
            $message = sprintf('Email %s is already registered', $e->emailAddress()->toString());

            return $this->responseFormatter->formatError($response, $message);
        } catch (RestrictedAccess $e) {
            return $this->responseFormatter->formatError($response, 'Not allowed', 403);
        }
    }
}
