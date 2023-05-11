<?php

declare(strict_types = 1);

namespace App\Model\Grid;

/**
 * Class Sorter
 *
 * @package App\Model\Grid
 */
final class Sorter
{

    /**
     * Sorter constructor.
     *
     * @param string $property
     * @param string $direction
     */
    public function __construct(private string $property, private string $direction)
    {
    }

    /**
     * @return string
     */
    public function getProperty(): string
    {
        return $this->property;
    }

    /**
     * @return string
     */
    public function getDirection(): string
    {
        return $this->direction;
    }
}
