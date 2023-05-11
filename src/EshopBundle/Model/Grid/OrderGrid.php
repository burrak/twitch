<?php

declare(strict_types = 1);

namespace App\EshopBundle\Model\Grid;

use App\Model\Grid\GridAbstract;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class OrderGrid extends GridAbstract
{

    protected function getQueryBuilder(EntityRepository $entityRepository, array $filters, array $sorters): QueryBuilder
    {
        return $entityRepository->createQueryBuilder('o')->select(['o']);
    }

    protected function getConditions(): array
    {
        return [
            'order.createdAt' => 'o.createdAt',
            'order.status' => 'o.status',
            'order.firstname' => 'o.firstName',
            'order.surname' => 'o.surname',
        ];
    }

    protected function getSortations(): array
    {
        return $this->getConditions();
    }
}
