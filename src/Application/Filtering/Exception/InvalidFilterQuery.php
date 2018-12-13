<?php

declare(strict_types=1);

namespace Caloriary\Application\Filtering\Exception;

use Doctrine\ORM\Query\QueryException;
use Nette\Utils\Strings;

final class InvalidFilterQuery extends \RuntimeException
{
    public function __construct(string $error)
    {
        parent::__construct(sprintf(
            'Invalid filter query: %s',
            $error
        ));
    }


    public static function fromQueryException(QueryException $e): self
    {
        $pattern = '/^.*Error: /';
        $error = Strings::replace($e->getMessage(), $pattern, '');

        return new self($error);
    }
}
