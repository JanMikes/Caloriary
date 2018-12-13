<?php

declare(strict_types=1);

namespace Caloriary\Authentication\Value;

class PasswordHash
{
    /**
     * @var string
     */
    private $hash;


    /**
     * @throws \InvalidArgumentException
     */
    public static function fromString(string $string): self
    {
        if (0 !== strpos($string, '$')) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid password hash "%s" provided',
                $string
            ));
        }

        $instance = new self();
        $instance->hash = $string;

        return $instance;
    }


    public function toString(): string
    {
        return $this->hash;
    }


    private function __construct()
    {
    }
}
