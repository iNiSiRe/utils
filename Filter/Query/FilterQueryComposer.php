<?php

namespace PrivateDev\Utils\Filter\Query;

use Doctrine\ORM\QueryBuilder;
use PrivateDev\Utils\Filter\FilterInterface;
use PrivateDev\Utils\Filter\Model\Range;

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

            // String, numeric, bool
            if (is_string($value) || is_numeric($value) || is_bool($value)) {
                $builder
                    ->andWhere("{$alias}.{$key} = :{$key}_value")
                    ->setParameter("{$key}_value", $value);
            }

            // Range
            if (is_object($value) && $value instanceof Range) {

                if ($value->getFrom()) {
                    $builder
                        ->andWhere("{$alias}.{$key} >= :{$key}_from")
                        ->setParameter("{$key}_from", $value->getFrom());
                }

                if ($value->getTo()) {
                    $builder
                        ->andWhere("{$alias}.{$key} <= :{$key}_to")
                        ->setParameter("{$key}_to", $value->getTo());
                }
            }
        }

        $builder->setMaxResults($filter->getCollectionMaxSize());
    }
}