<?php

declare(strict_types=1);

namespace Caloriary\Infrastructure\Application\Middleware;

use BrandEmbassy\Slim\Middleware;
use BrandEmbassy\Slim\Request\RequestInterface;
use BrandEmbassy\Slim\Response\ResponseInterface;
use Caloriary\Infrastructure\Application\Response\ResponseFormatter;
use Nette\Utils\JsonException;

final class ValidJSONBodyMiddleware implements Middleware
{
    /**
     * @var ResponseFormatter
     */
    private $responseFormatter;


    public function __construct(ResponseFormatter $responseFormatter)
    {
        $this->responseFormatter = $responseFormatter;
    }


    public function __invoke(RequestInterface $request, ResponseInterface $response, callable $next): ResponseInterface
    {
        $body = (string) $request->getBody();

        if ($body !== '') {
            try {
                $request->getDecodedJsonFromBody();
            } catch (JsonException $e) {
                return $this->responseFormatter->formatError($response, 'Request body is not valid JSON!');
            }
        }

        return $next($request, $response);
    }
}
