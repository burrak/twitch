<?php

declare(strict_types = 1);

namespace App\EshopBundle\Model\Grid;

use App\Model\Grid\GridAbstract;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class ProductGrid extends GridAbstract
{

    protected function getQueryBuilder(EntityRepository $entityRepository, array $filters, array $sorters): QueryBuilder
    {
        return $entityRepository->createQueryBuilder('p')->select(['p']);
    }

    protected function getConditions(): array
    {
        return [
            'product.title' => 'p.title',
            'product.price' => 'p.price',
        ];
    }

    protected function getSortations(): array
    {
        return $this->getConditions();
    }
}
