<?php

namespace PrivateDev\Utils\Filter\Query;

use Doctrine\DBAL\Query\QueryBuilder;
use PrivateDev\Utils\Filter\FilterInterface;

/**
 * Compose query by filter data
 */
class FilterQueryComposer
{
    /**
     * Fill up QueryBuilder by filter data
     *
     * @param QueryBuilder    $builder
     * @param string          $alias
     * @param FilterInterface $filter
     */
    public function compose(QueryBuilder $builder, string $alias, FilterInterface $filter)
    {
        foreach ($filter->getFilter() as $key => $value) {

            if (!is_string($value) || !is_numeric($value) || !is_bool($value)) {
                continue;
            }

            $builder
                ->andWhere("{$alias}.{$key} = :{$key}_value")
                ->setParameter("{$key}_value", $value);
        }

        $builder->setMaxResults($filter->getCollectionMaxSize());
    }
}