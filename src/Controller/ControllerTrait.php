<?php

declare(strict_types = 1);

namespace App\Controller;

use App\Model\Grid\Filter;
use App\Model\Grid\Grid;
use App\Model\Grid\Pager;
use App\Model\Grid\Sorter;
use Symfony\Component\HttpFoundation\InputBag;

trait ControllerTrait
{
    /**
     * @param InputBag $inputBag
     *
     * @return Grid
     */
    private function createGridDb(InputBag $inputBag): Grid
    {
        $filters = [];
        $sorters = [];

        /** @var mixed[][] $queryFilters */
        $queryFilters = $inputBag->all('filter');
        /** @var mixed[] $querySorters */
        $querySorters = $inputBag->all('sorter');

        foreach ($querySorters as $property => $direction) {
            $sorters[] = new Sorter($property, $direction);
        }

        foreach ($queryFilters as $property => $values) {
            foreach ($values as $operator => $innerValues) {
                if (!strlen($innerValues[0]))
                {
                    continue;
                }

                $filters[] = new Filter($property, $operator, $innerValues);
            }
        }

        return new Grid(
            $filters,
            $sorters,
            new Pager((int) $inputBag->get('page', 1), (int) $inputBag->get('itemsPerPage', 10))
        );
    }
}
