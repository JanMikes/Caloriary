<?php

declare(strict_types=1);

namespace Caloriary\Infrastructure\Application\Response;

use BrandEmbassy\Slim\Response\ResponseInterface;

final class ResponseFormatter
{
    public function formatError(ResponseInterface $response, string $error, int $code = 400): ResponseInterface
    {
        return $response->withJson([
            'error' => $error,
        ], $code);
    }
}
