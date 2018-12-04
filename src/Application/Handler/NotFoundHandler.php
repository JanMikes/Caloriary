<?php declare (strict_types=1);

namespace Caloriary\Application\Handler;

use BrandEmbassy\Slim\ErrorHandler;
use BrandEmbassy\Slim\Request\RequestInterface;
use BrandEmbassy\Slim\Response\ResponseInterface;

final class NotFoundHandler implements ErrorHandler
{
	public function __invoke(RequestInterface $request, ResponseInterface $response, ?\Throwable $e = null): ResponseInterface
	{
		return $response->withJson([
			'error' => 'not_found',
			'message' => 'Requested resource not found!',
		], 404);
	}
}
