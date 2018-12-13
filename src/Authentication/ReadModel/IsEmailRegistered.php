<?php

declare(strict_types=1);

namespace Caloriary\Authentication\ReadModel;

use Caloriary\Authentication\Value\EmailAddress;

interface IsEmailRegistered
{
    public function __invoke(EmailAddress $emailAddress): bool;
}
