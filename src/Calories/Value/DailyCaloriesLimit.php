<?php

declare(strict_types=1);

namespace Caloriary\Calories\Value;

class DailyCaloriesLimit
{
    /**
     * @var int
     */
    private $limit;


    /**
     * @throws \InvalidArgumentException
     */
    public static function fromInteger(int $limit): self
    {
        if ($limit < 0) {
            throw new \InvalidArgumentException('Limit of daily calories must not be lower than 0.');
        }

        $instance = new self();
        $instance->limit = $limit;

        return $instance;
    }


    public static function createUnlimited(): self
    {
        return self::fromInteger(0);
    }


    public function toInteger(): int
    {
        return $this->limit;
    }


    public function isLimited(): bool
    {
        return $this->limit > 0;
    }


    private function __construct()
    {
    }
}
