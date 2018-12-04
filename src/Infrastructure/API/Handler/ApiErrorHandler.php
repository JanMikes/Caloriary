<?php declare (strict_types=1);

namespace Caloriary\Infrastructure\API\Handler;

use BrandEmbassy\Slim\ErrorHandler;
use BrandEmbassy\Slim\Request\RequestInterface;
use BrandEmbassy\Slim\Response\ResponseInterface;
use Tracy\ILogger;

final class ApiErrorHandler implements ErrorHandler
{
	/**
	 * @var ILogger
	 */
	private $logger;

	public function __construct(ILogger $logger)
	{
		$this->logger = $logger;
	}


	public function __invoke(RequestInterface $request, ResponseInterface $response, ?\Throwable $e = null): ResponseInterface
	{
		$error = $e !== null ? $e->getMessage() : 'Unknown error.';
		$statusCode = 500;
		$errorType = 'server_error';

		$this->logger->log($e, ILogger::EXCEPTION);

		return $response->withJson(['error' => $errorType, 'message' => $error], $statusCode);
	}
}
