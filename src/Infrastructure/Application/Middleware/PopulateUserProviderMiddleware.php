<?php declare (strict_types=1);

namespace Caloriary\Infrastructure\Application\Middleware;

use BrandEmbassy\Slim\Middleware;
use BrandEmbassy\Slim\Request\RequestInterface;
use BrandEmbassy\Slim\Response\ResponseInterface;
use Caloriary\Authentication\Value\EmailAddress;
use Caloriary\Infrastructure\Authentication\UserProvider;

final class PopulateUserProviderMiddleware implements Middleware
{
	/**
	 * @var UserProvider
	 */
	private $userProvider;


	public function __construct(UserProvider $userProvider)
	{
		$this->userProvider = $userProvider;
	}


	public function __invoke(RequestInterface $request, ResponseInterface $response, callable $next): ResponseInterface
	{
		if (isset($request->getAttribute('token')['sub'])) {
			$this->userProvider->populateUser(
				EmailAddress::fromString($request->getAttribute('token')['sub'])
			);
		}

		return $next($request, $response);
	}
}
