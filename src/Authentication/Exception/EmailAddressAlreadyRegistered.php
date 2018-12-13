<?php

declare(strict_types=1);

namespace Caloriary\Authentication\Exception;

use Caloriary\Authentication\Value\EmailAddress;

final class EmailAddressAlreadyRegistered extends \RuntimeException
{
    /**
     * @var EmailAddress
     */
    private $emailAddress;


    public function __construct(EmailAddress $emailAddress)
    {
        $this->emailAddress = $emailAddress;

        parent::__construct();
    }


    public function emailAddress(): EmailAddress
    {
        return $this->emailAddress;
    }
}
