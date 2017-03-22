<?php

namespace PrivateDev\Utils\Builder;

use PrivateDev\Utils\Filter\Model\FilterInterface;
use PrivateDev\Utils\Order\OrderInterface;

class QueryBuilder extends AbstractQueryBuilder
{
    /**
     * @param FilterInterface $filter
     * @param string          $alias
     *
     * @return $this
     */
    public function setFilter(FilterInterface $filter, $alias = self::ALIAS)
    {
        $this->addFilter($filter, $filter->getRelationshipAlias());
        $this->builder->setMaxResults($filter->getCollectionMaxSize());

        return $this;
    }

    /**
     * @param OrderInterface $order
     * @param string         $alias
     *
     * @return $this
     */
    public function setOrder(OrderInterface $order, $alias = self::ALIAS)
    {
        $this->addOrder($order, $order->getRelationshipAlias());

        return $this;
    }

    /**
     * @param FilterInterface $filter
     * @param                 $alias
     */
    protected function addFilter(FilterInterface $filter, $alias)
    {
        if (count($filter->getJoins()) > 0) {
            foreach ($filter->getJoins() as $join) {
                $this->builder->add('join', [$join], true);
            }
        }

        foreach ($filter->getFilter() as $key => $value) {
            $this->addCondition($key, $value, $alias);
        }
    }

    /**
     * @param OrderInterface $order
     */
    protected function addOrder(OrderInterface $order, $alias)
    {
        if (count($order->getJoins()) > 0) {
            foreach ($order->getJoins() as $join) {
                $this->builder->add('join', [$join], true);
            }
        }

        foreach ($order->getOrder() as $key => $value) {
            $this->addOrderCondition($key, $value, $alias);
        }
    }

    /**
     * @param        $key
     * @param        $value
     * @param string $alias
     */
    protected function addCondition($key, $value, $alias = self::ALIAS)
    {
        if (is_object($value) && $value instanceof FilterInterface) {
            $this->addFilter($value, $key);

            return;
        }

        parent::addCondition($key, $value, $alias);
    }

    /**
     * @param        $key
     * @param        $value
     * @param string $alias
     */
    protected function addOrderCondition($key, $value, $alias = self::ALIAS)
    {
        if (is_object($value) && $value instanceof OrderInterface) {
            $this->addOrder($value, $key);

            return;
        }

        parent::addOrderCondition($key, $value, $alias);
    }
}