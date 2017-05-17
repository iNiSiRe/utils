<?php

namespace PrivateDev\Utils\Builder;

use PrivateDev\Utils\Filter\Pagination;

class PaginationQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @param Pagination $pagination
     *
     * @return $this
     */
    public function setPagination(Pagination $pagination)
    {
        $this->builder
            ->setFirstResult($pagination->getOffset())
            ->setMaxResults($pagination->getLimit());

        return $this;
    }

    /**
     * @param $key
     * @param $value
     * @param $alias
     */
    protected function addCondition($key, $value, $alias)
    {
        return;
    }

    /**
     * @return int
     */
    public function getTotalSize()
    {
        $builder = clone $this->builder;
        $builder->resetDQLParts(['select', 'groupBy']);

        $size = $builder
            ->select(sprintf('COUNT(DISTINCT %s)', $builder->getRootAliases()[0]))
            ->setFirstResult(null)
            ->setMaxResults(null)
            ->getQuery()
            ->getSingleScalarResult();

        return $size;
    }
}