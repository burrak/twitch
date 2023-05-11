<?php

declare(strict_types = 1);

namespace App\Model\Grid;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use LogicException;

/**
 * Class GridAbstract
 *
 * @package App\Model\Grid
 *
 * @template T of object
 */
abstract class GridAbstract
{

    public const LT = 'lt';
    public const LTE = 'lte';
    public const GT = 'gt';
    public const GTE = 'gte';
    public const IN = 'in';
    public const LIKE = 'like';
    public const BETWEEN = 'between';

    protected const CONDITIONS = [
        self::LT => '<',
        self::LTE => '<=',
        self::GT => '>',
        self::GTE => '>=',
        self::IN => 'IN',
        self::LIKE => 'LIKE',
        self::BETWEEN => 'BETWEEN',
    ];

    protected const SORTATIONS = [
        self::ASC => self::ASC,
        self::DESC => self::DESC,
    ];

    protected const ASC = 'asc';
    protected const DESC = 'desc';

    /**
     * @param EntityRepository<T> $entityRepository
     * @param Filter[] $filters
     * @param Sorter[] $sorters
     *
     * @return QueryBuilder
     */
    abstract protected function getQueryBuilder(
        EntityRepository $entityRepository,
        array $filters,
        array $sorters
    ): QueryBuilder;

    /**
     * @return mixed[]
     */
    abstract protected function getConditions(): array;

    /**
     * @return mixed[]
     */
    abstract protected function getSortations(): array;

    /**
     * GridAbstract constructor.
     *
     * @param EntityRepository<T> $entityRepository
     */
    public function __construct(private EntityRepository $entityRepository)
    {
    }

    /**
     * @param Filter[] $filters
     * @param Sorter[] $sorters
     * @param Pager $pager
     * @param Filter[] $customFilters
     * @param Sorter[] $customSorters
     *
     * @return mixed[]
     */
    public function getResult(
        array $filters,
        array $sorters,
        Pager $pager,
        array $customFilters = [],
        array $customSorters = []
    ): array {
        $queryBuilder = $this->getQueryBuilder($this->entityRepository, $filters, $sorters);
        $queryBuilder = $this->processConditions($queryBuilder, array_merge($filters, $customFilters));
        $queryBuilder = $this->processSortations($queryBuilder, array_merge($sorters, $customSorters));
        $queryBuilder = $this->processPagination($queryBuilder, $pager);
        $paginator = new Paginator($queryBuilder->getQuery()->setHint(Query::HINT_REFRESH, true));
        $items     = iterator_to_array($paginator);

        if (is_array($items[0] ?? null) && is_object($items[0][0] ?? null)) {
            foreach ($items as $key => $item) {
                $object = array_shift($item);

                if (!$object)
                {
                    continue;
                }

                foreach ($item as $innerKey => $innerItem) {
                    $object->{sprintf('set%s', ucfirst($innerKey))}($innerItem);
                }
                    $items[$key] = $object;
            }
        }

        return [$items, $paginator->count()];
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param Filter[] $filters
     *
     * @return QueryBuilder
     */
    private function processConditions(QueryBuilder $queryBuilder, array $filters): QueryBuilder
    {
        $innerConditions = $this->getConditions();
        $iterator = 0;

        foreach ($filters as $filter) {
            if (!isset($innerConditions[$filter->getProperty()])) {
                throw new LogicException(
                    sprintf(
                        "Unknown filter column '%s', please use one of '%s' or create new one via %s::getConditions().",
                        $filter->getProperty(),
                        implode("', '", array_keys($innerConditions)),
                        static::class
                    )
                );
            }

            if (!isset(self::CONDITIONS[$filter->getOperator()])) {
                throw new LogicException(
                    sprintf(
                        "Unknown filter operator '%s', please use one of '%s'",
                        $filter->getOperator(),
                        implode("', '", array_keys(self::CONDITIONS))
                    )
                );
            }

            if (is_string($innerConditions[$filter->getProperty()])) {
                $property = $innerConditions[$filter->getProperty()];
                $andWhere = match ($filter->getOperator()) {
                    self::IN => sprintf('%s IN (?%s)', $property, $iterator),
                    self::BETWEEN => sprintf('%s BETWEEN ?%s AND ?%s', $property, $iterator++, $iterator++),
                    default => sprintf('%s %s ?%s', $property, self::CONDITIONS[$filter->getOperator()], $iterator),
                };
                $parameter = match ($filter->getOperator()) {
                    self::IN => $filter->getValues(),
                    self::LIKE => sprintf('%%%s%%', $filter->getValues()[0]),
                    default => $filter->getValues()[0],
                };

                if ($filter->getOperator() === self::BETWEEN) {
                    $queryBuilder
                        ->andWhere($andWhere)
                        ->setParameter($iterator - 2, $filter->getValues()[0])
                        ->setParameter($iterator - 1, $filter->getValues()[1]);
                } else {
                    $queryBuilder
                        ->andWhere($andWhere)
                        ->setParameter($iterator++, $parameter);
                }
            } else {
                $queryBuilder = $innerConditions[$filter->getProperty()](
                    $queryBuilder,
                    $filter->getOperator(),
                    $filter->getValues()
                );
            }
        }

        return $queryBuilder;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param Sorter[] $sorters
     *
     * @return QueryBuilder
     */
    private function processSortations(QueryBuilder $queryBuilder, array $sorters): QueryBuilder
    {
        $innerSortations = $this->getSortations();
        $isFirstSortation = true;

        foreach ($sorters as $sorter) {
            if (!isset($innerSortations[$sorter->getProperty()])) {
                throw new LogicException(
                    sprintf(
                        "Unknown sorter column '%s', please use one of '%s' or create new one via %s::getSortations().",
                        $sorter->getProperty(),
                        implode("', '", array_keys($innerSortations)),
                        static::class
                    )
                );
            }

            if (!isset(self::SORTATIONS[$sorter->getDirection()])) {
                throw new LogicException(
                    sprintf(
                        "Unknown sorter direction '%s', please use one of '%s'",
                        $sorter->getDirection(),
                        implode("', '", array_keys(self::SORTATIONS))
                    )
                );
            }

            if (is_string($innerSortations[$sorter->getProperty()])) {
                if ($isFirstSortation) {
                    $queryBuilder->orderBy($innerSortations[$sorter->getProperty()], $sorter->getDirection());
                } else {
                    $queryBuilder->addOrderBy($innerSortations[$sorter->getProperty()], $sorter->getDirection());
                }
            } else {
                $queryBuilder = $innerSortations[$sorter->getProperty()](
                    $queryBuilder,
                    $sorter->getDirection()
                );
            }

            $isFirstSortation = false;
        }

        return $queryBuilder;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param Pager $pager
     *
     * @return QueryBuilder
     */
    private function processPagination(QueryBuilder $queryBuilder, Pager $pager): QueryBuilder
    {
        return $queryBuilder
            ->setMaxResults(max(0, $pager->getItemsPerPage()))
            ->setFirstResult(max(0, ($pager->getPage() - 1) * $pager->getItemsPerPage()));
    }
}
