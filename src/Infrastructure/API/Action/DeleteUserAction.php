<?php declare (strict_types=1);

namespace Caloriary\Infrastructure\API\Action;

use BrandEmbassy\Slim\ActionHandler;
use BrandEmbassy\Slim\Request\RequestInterface;
use BrandEmbassy\Slim\Response\ResponseInterface;

final class DeleteUserAction implements ActionHandler
{
	public function __invoke(RequestInterface $request, ResponseInterface $response, array $arguments = []): ResponseInterface
	{
		return $response->withJson([], 204);
	}
}
