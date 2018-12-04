<?php declare (strict_types=1);

namespace Caloriary\Application\Handler;

use BrandEmbassy\Slim\Request\RequestInterface;
use BrandEmbassy\Slim\Response\ResponseInterface;

final class NotAllowedHandler
{
	public function __invoke(RequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		return $response->withJson([
			'error' => 'method_not_allowed',
			'message' => 'HTTP method not allowed!',
		], 405);
	}
}
