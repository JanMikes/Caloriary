<?php

declare(strict_types=1);

namespace JanMikes\Nutritionix;

final class Food
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $calories;


    public function __construct(string $name, int $calories)
    {
        $this->name = $name;
        $this->calories = $calories;
    }


    public function name(): string
    {
        return $this->name;
    }


    public function calories(): int
    {
        return $this->calories;
    }
}
