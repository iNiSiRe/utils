<?php

namespace PrivateDev\Utils\Filter;

use Doctrine\ORM\EntityRepository;
use PrivateDev\Utils\Filter\Model\DeepFilterInterface;
use PrivateDev\Utils\Filter\Model\FilterInterface;
use Symfony\Component\Form\FormInterface;

class DeepQueryBuilder extends QueryBuilder
{
    /**
     * @param FilterInterface $filter
     *
     * @return $this
     */
    public function setFilter(FilterInterface $filter, $alias = self::ALIAS)
    {
        $this->createQueryBuilder($filter);
        $this->addDeepFilter($filter, $filter->getRelationshipAlias());
        $this->builder->setMaxResults($filter->getCollectionMaxSize());

        return $this;
    }

    /**
     * @param DeepFilterInterface $filter
     */
    protected function addDeepFilter(DeepFilterInterface $filter, $alias)
    {
        if (count($filter->getJoins()) > 0) {
            foreach ($filter->getJoins() as $join) {
                $this->builder->add('join', [$join], true);
            }
        }

        $this->addFilter($filter, $alias);
    }

    /**
     * @param FilterInterface $filter
     * @param string          $alias
     */
    protected function addFilter(FilterInterface $filter, $alias)
    {
        foreach ($filter->getFilter() as $key => $value) {
            $this->addCondition($key, $value, $alias);
        }
    }

    /**
     * @param $key
     * @param $value
     */
    protected function addCondition($key, $value, $alias = self::ALIAS)
    {
        if (is_object($value) && $value instanceof DeepFilterInterface) {
            $this->addDeepFilter($value, $key);

            return;
        }

        if (is_object($value) && $value instanceof FilterInterface) {
            $this->addFilter($value, $key);

            return;
        }

        parent::addCondition($key, $value, $alias);
    }
}