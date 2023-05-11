<?php

declare(strict_types = 1);

namespace App\Model\Grid;

/**
 * Class Filter
 *
 * @package App\Model\Grid
 */
final class Filter
{

    /**
     * Filter constructor.
     *
     * @param string $property
     * @param string $operator
     * @param mixed[] $values
     */
    public function __construct(private string $property, private string $operator, private array $values)
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
    public function getOperator(): string
    {
        return $this->operator;
    }

    /**
     * @return mixed[]
     */
    public function getValues(): array
    {
        return $this->values;
    }
}
