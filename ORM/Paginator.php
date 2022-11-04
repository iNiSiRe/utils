<?php

namespace PrivateDev\Utils\ORM;

use Traversable;
use ArrayIterator;
use IteratorAggregate;
use Doctrine\ORM\QueryBuilder;

class Paginator implements IteratorAggregate
{
    /**
     * @var QueryBuilder
     */
    private $builder;

    /**
     * Paginator constructor.
     *
     * @param QueryBuilder           $builder
     */
    public function __construct(QueryBuilder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * Returns the query.
     *
     * @return QueryBuilder
     */
    public function getBuilder()
    {
        return $this->builder;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator() : Traversable
    {
        $alias = $this->builder->getRootAliases()[0];
        $ids = (clone $this->builder)
            ->select($alias . '.id')
            ->addGroupBy($alias . '.id')
            ->getQuery()
            ->getScalarResult();

        // don't do this for an empty id array
        if (count($ids) == 0) {
            return new ArrayIterator([]);
        }

        return new ArrayIterator(
            (clone $this->builder)
                ->resetDQLPart('where')
                ->where($alias . '.id IN (:ids)')
                ->setFirstResult(null)
                ->setMaxResults(null)
                ->setParameters(['ids' => $ids])
                ->getQuery()
                ->getResult()
        );
    }
}
