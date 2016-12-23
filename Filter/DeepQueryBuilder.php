<?php
/**
 * Created by PhpStorm.
 * User: user18
 * Date: 23.12.16
 * Time: 13:09
 */

namespace PrivateDev\Utils\Filter;

use Doctrine\ORM\EntityRepository;
use PrivateDev\Utils\Filter\Model\AbstractDeepFilter;
use PrivateDev\Utils\Filter\Model\FilterInterface;

class DeepQueryBuilder extends QueryBuilder
{
    /**
     * FilterQueryBuilder constructor
     *
     * @param EntityRepository $repository
     */
    public function __construct(EntityRepository $repository)
    {
        parent::__construct($repository);
    }

    /**
     * @param AbstractDeepFilter $filter
     *
     * @return $this
     */
    public function setFilter(FilterInterface $filter, $alias = self::ALIAS)
    {
        if ($filter instanceof AbstractDeepFilter) {
            if (count($filter->getJoins()) > 0) {
                foreach ($filter->getJoins() as $join) {
                    $this->builder->add('join', [$join], true);
                }
            }
        }

        return parent::setFilter($filter);
    }

    /**
     * @param $key
     * @param $value
     */
    protected function addCondition($key, $value, $alias = self::ALIAS)
    {
        parent::addCondition($key, $value, $alias);

        if (is_object($value) && $value instanceof FilterInterface) {
            parent::setFilter($value, $key);
        }
    }
}