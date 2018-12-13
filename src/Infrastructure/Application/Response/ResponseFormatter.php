<?php

declare(strict_types=1);

namespace Caloriary\Infrastructure\Application\Response;

use BrandEmbassy\Slim\Response\ResponseInterface;

final class ResponseFormatter
{
    /**
     * @param string[]|string $errors
     */
    public function formatError(ResponseInterface $response, $errors, int $code = 400): ResponseInterface
    {
        return $response->withJson([
            'errors' => is_array($errors) ? $errors : [$errors],
        ], $code);
    }
}
