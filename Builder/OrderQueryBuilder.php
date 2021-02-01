<?php

namespace PrivateDev\Utils\Builder;

use Doctrine\Common\Collections\Criteria;
use PrivateDev\Utils\Builder\Query\QueryInterface;

class OrderQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @var array
     */
    protected static $criteria = [
        Criteria::ASC,
        Criteria::DESC
    ];

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

        if (is_string($value) && in_array($value, self::$criteria)) {
            $this->builder->addOrderBy(sprintf('%s.%s', $alias, $key), $value);
        }
    }
}
