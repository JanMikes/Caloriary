<?php

declare(strict_types=1);

namespace Caloriary\Infrastructure\Application\Response;

use Nette\Utils\Paginator;

final class PaginatorResponseTransformer
{
    /**
     * @return mixed[]
     */
    public function toArray(Paginator $paginator): array
    {
        return [
            'page' => $paginator->getPage(),
            'limit' => $paginator->getItemsPerPage(),
            'pages' => $paginator->getPageCount(),
            'totalCount' => $paginator->getItemCount(),
        ];
    }
}
