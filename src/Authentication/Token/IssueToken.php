<?php

declare(strict_types=1);

namespace Caloriary\Authentication\Token;

use Caloriary\Authentication\Value\EmailAddress;

interface IssueToken
{
    public function __invoke(EmailAddress $emailAddress, string $audience): string;
}
