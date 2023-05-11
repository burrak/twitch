<?php

declare(strict_types = 1);

namespace App\Model\Grid;

/**
 * Class Grid
 *
 * @package App\Model\Grid
 */
final class Grid
{
    /**
     * Grid constructor.
     *
     * @param Filter[] $filters
     * @param Sorter[] $sorters
     * @param Pager $pager
     */
    public function __construct(private array $filters, private array $sorters, private Pager $pager)
    {
    }

    /**
     * @return Filter[]
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @return Sorter[]
     */
    public function getSorters(): array
    {
        return $this->sorters;
    }

    /**
     * @return Pager
     */
    public function getPager(): Pager
    {
        return $this->pager;
    }
}
