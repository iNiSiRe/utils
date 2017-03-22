<?php

namespace PrivateDev\Utils\Builder;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use PrivateDev\Utils\Builder\Query\QueryInterface;

abstract class AbstractQueryBuilder
{
    /**
     * @var QueryBuilder
     */
    protected $builder;

    /**
     * @var string
     */
    protected $alias;

    /**
     * @param QueryBuilder $builder
     */
    public function __construct(QueryBuilder $builder)
    {
        $this->builder = $builder;
        $this->alias = $builder->getRootAliases()[0];
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    protected function createPlaceholder($key)
    {
        return str_replace('.', '_', $key);
    }

    /**
     * @param $key
     * @param $value
     * @param $alias
     */
    abstract protected function addCondition($key, $value, $alias);

    /**
     * @return Query
     */
    public function getQuery()
    {
        return $this->builder->getQuery();
    }

    /**
     *
     * @param QueryInterface $query
     *
     * @return $this
     */
    public function setQuery(QueryInterface $query)
    {
        $this->addQuery($query, $this->alias);

        return $this;
    }

    /**
     * @param QueryInterface  $query
     * @param                 $alias
     */
    protected function addQuery(QueryInterface $query, $alias)
    {
        if (count($query->getJoins()) > 0) {
            foreach ($query->getJoins() as $join) {
                $this->builder->add('join', [$join], true);
            }
        }

        foreach ($query->getQuery() as $key => $value) {
            $this->addCondition($key, $value, $alias);
        }
    }
}
