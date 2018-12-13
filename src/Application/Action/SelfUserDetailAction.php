<?php declare (strict_types=1);

namespace Caloriary\Application\Action;

use BrandEmbassy\Slim\ActionHandler;
use BrandEmbassy\Slim\Request\RequestInterface;
use BrandEmbassy\Slim\Response\ResponseInterface;
use Caloriary\Authentication\Exception\UserNotFound;
use Caloriary\Infrastructure\Application\Response\ResponseFormatter;
use Caloriary\Infrastructure\Application\Response\UserResponseTransformer;
use Caloriary\Infrastructure\Authentication\UserProvider;

final class SelfUserDetailAction implements ActionHandler
{
	/**
	 * @var ResponseFormatter
	 */
	private $responseFormatter;

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
		UserResponseTransformer $userResponseTransformer,
		UserProvider $userProvider
	)
	{
		$this->responseFormatter = $responseFormatter;
		$this->userResponseTransformer = $userResponseTransformer;
		$this->userProvider = $userProvider;
	}


	/**
	 * @param string[] $arguments
	 */
	public function __invoke(RequestInterface $request, ResponseInterface $response, array $arguments = []): ResponseInterface
	{
		try {
			$currentUser = $this->userProvider->currentUser();

			return $response->withJson($this->userResponseTransformer->toArray($currentUser), 200);
		}

		catch (UserNotFound $e) {
			return $this->responseFormatter->formatError($response, 'User not found!', 404);
		}
	}
}
