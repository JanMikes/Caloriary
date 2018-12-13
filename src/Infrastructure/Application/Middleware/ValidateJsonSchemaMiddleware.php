<?php

declare(strict_types=1);

namespace Caloriary\Infrastructure\Application\Middleware;

use BrandEmbassy\Slim\Middleware;
use BrandEmbassy\Slim\Request\RequestInterface;
use BrandEmbassy\Slim\Response\ResponseInterface;
use Caloriary\Infrastructure\Application\Response\ResponseFormatter;
use JsonSchema\Validator;
use Nette\Utils\Json;
use Nette\Utils\JsonException;
use Slim\Route;

final class ValidateJsonSchemaMiddleware implements Middleware
{
    /**
     * @var ResponseFormatter
     */
    private $responseFormatter;

    /**
     * @var array
     */
    private $mappings;


    /**
     * @param string[] $mappings
     */
    public function __construct(array $mappings, ResponseFormatter $responseFormatter)
    {
        $this->responseFormatter = $responseFormatter;
        $this->mappings = $mappings;
    }


    public function __invoke(RequestInterface $request, ResponseInterface $response, callable $next): ResponseInterface
    {
        $body = (string) $request->getBody();
        $route = $request->getAttribute('route');

        if ($body !== '') {
            try {
                $data = $request->getDecodedJsonFromBody();
            } catch (JsonException $e) {
                return $this->responseFormatter->formatError($response, 'Request body is not valid JSON!');
            }
        }

        if ($route instanceof Route) {
            $callableClass = (string) $route->getCallable();

            if (isset($this->mappings[$callableClass])) {
                $schema = Json::decode(file_get_contents($this->mappings[$callableClass]));

                $validator = new Validator();
                $validator->coerce($data, $schema);

                if (!$validator->isValid()) {
                    $errors = array_map(function(array $error) {
                        return $error['message'];
                    }, $validator->getErrors());

                    return $this->responseFormatter->formatError($response, $errors);
                }
            }
        }


        return $next($request, $response);
    }
}
