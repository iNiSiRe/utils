<?php

namespace PrivateDev\Utils\Filter;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use PrivateDev\Utils\Filter\Model\FilterInterface;
use PrivateDev\Utils\Filter\Model\Pagination;
use PrivateDev\Utils\Filter\Model\PartialMatchText;
use PrivateDev\Utils\Filter\Model\Range;

class QueryBuilder
{
    const ALIAS = 'a';

    /**
     * @var EntityRepository
     */
    protected $repository;

    /**
     * @var \Doctrine\ORM\QueryBuilder
     */
    protected $builder;

    /**
     * FilterQueryBuilder constructor
     *
     * @param EntityRepository $repository
     */
    public function __construct(EntityRepository $repository)
    {
        $this->repository = $repository;
        $this->builder = $repository->createQueryBuilder(self::ALIAS);
    }

    /**
     * @param $key
     * @param $value
     */
    protected function addCondition($key, $value, $alias = self::ALIAS)
    {
        // String, numeric, bool
        if (is_string($value) || is_numeric($value) || is_bool($value)) {
            $this->builder
                ->andWhere(sprintf('%1$s.%2$s = :%2$s_value', $alias, $key))
                ->setParameter(sprintf('%s_value', $key), $value);
        }

        // DateTime
        if ($value instanceof \DateTime) {
            $this->builder
                ->andWhere(sprintf('%1$s.%2$s = :%2$s_value', $alias, $key))
                ->setParameter(sprintf('%s_value', $key), $value);
        }

        // Range
        if (is_object($value) && $value instanceof Range) {

            if ($value->getFrom()) {
                $this->builder
                    ->andWhere(sprintf('%1$s.%2$s >= :%2$s_from', $alias, $key))
                    ->setParameter(sprintf('%s_from', $key), $value->getFrom());
            }

            if ($value->getTo()) {
                $this->builder
                    ->andWhere(sprintf('%1$s.%2$s <= :%2$s_to', $alias, $key))
                    ->setParameter(sprintf('%s_to', $key), $value->getTo());
            }
        }

        if (is_object($value) && $value instanceof PartialMatchText) {
            $this->builder
                ->andWhere(sprintf('%1$s.%2$s LIKE :%2$s_value', $alias, $key))
                ->setParameter(sprintf('%s_value', $key), $value->getText() . '%');
        }
    }

    /**
     * @param FilterInterface $filter
     *
     * @return $this
     */
    public function setFilter(FilterInterface $filter, $alias = self::ALIAS)
    {
        foreach ($filter->getFilter() as $key => $value) {
            $this->addCondition($key, $value, $alias);
        }

        $this->builder->setMaxResults($filter->getCollectionMaxSize());

        return $this;
    }

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
     * @param array $order
     *
     * @return $this
     */
    public function setOrder(array $order)
    {
        foreach ($order as $field => $type)
        {
            $this->builder->addOrderBy(sprintf('%s.%s', self::ALIAS, $field), $type);
        }

        return $this;
    }

    /**
     * @return Query
     */
    public function getQuery()
    {
        return $this->builder->getQuery();
    }

    /**
     * @return int
     */
    public function getTotalSize()
    {
        $builder = clone $this->builder;
        $builder->resetDQLPart('select');

        $size = $builder
            ->select(sprintf('COUNT(%s)', self::ALIAS))
            ->setFirstResult(null)
            ->setMaxResults(null)
            ->getQuery()
            ->getSingleScalarResult();

        return $size;
    }
}
