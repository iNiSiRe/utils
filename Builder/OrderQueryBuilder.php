<?php

namespace PrivateDev\Utils\Builder;

use PrivateDev\Utils\Builder\Query\QueryInterface;

class OrderQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @param        $key
     * @param        $value
     * @param string $alias
     */
    protected function addCondition($key, $value, $alias)
    {
        if (is_object($value) && $value instanceof QueryInterface) {
            $this->addQuery($value, $key);

            return;
        }

        if (is_string($value)) {
            $this->builder->addOrderBy(sprintf('%s.%s', $alias, $key), $value);
        }
    }
}
