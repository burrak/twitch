<?php

declare(strict_types = 1);

namespace App\Model\Grid;

/**
 * Class Pager
 *
 * @package App\Model\Grid
 */
final class Pager
{

    /**
     * Pager constructor.
     *
     * @param int $page
     * @param int $itemsPerPage
     */
    public function __construct(private int $page, private int $itemsPerPage)
    {
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @return int
     */
    public function getItemsPerPage(): int
    {
        return $this->itemsPerPage;
    }
}
