<?php

namespace PrivateDev\Utils\Filter;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use PrivateDev\Utils\Filter\Model\EmptyData;
use PrivateDev\Utils\Filter\Model\FilterInterface;
use PrivateDev\Utils\Filter\Model\Pagination;
use PrivateDev\Utils\Filter\Model\PartialMatchText;
use PrivateDev\Utils\Filter\Model\Range;
use PrivateDev\Utils\Order\OrderInterface;

abstract class AbstractQueryBuilder
{
    const ALIAS = 'a';

    /**
     * @var \Doctrine\ORM\QueryBuilder
     */
    protected $builder;

    /**
     * FilterQueryBuilder constructor
     *
     * @param QueryBuilder $builder
     */
    public function __construct(QueryBuilder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * @param FilterInterface $filter
     * @param string          $alias
     *
     * @return $this
     */
    abstract public function setFilter(FilterInterface $filter, $alias = self::ALIAS);

    /**
     * @param OrderInterface $order
     * @param                $alias
     *
     * @return $this
     */
    abstract public function setOrder(OrderInterface $order, $alias = self::ALIAS);

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
     * @param        $key
     * @param        $value
     * @param string $alias
     */
    protected function addCondition($key, $value, $alias = self::ALIAS)
    {
        switch (true) {
            // String, numeric, bool
            case (is_string($value) || is_numeric($value) || is_bool($value)): {
                $this->builder
                    ->andWhere(sprintf('%1$s.%2$s = :%1$s_%3$s_value', $alias, $key, $this->createPlaceholder($key)))
                    ->setParameter(sprintf('%s_%s_value', $alias, $this->createPlaceholder($key)), $value);
            } break;

            // DateTime
            case ($value instanceof \DateTime): {
                $this->builder
                    ->andWhere(sprintf('%1$s.%2$s = :%1$s_%3$s_value', $alias, $key, $this->createPlaceholder($key)))
                    ->setParameter(sprintf('%s_%s_value', $alias, $this->createPlaceholder($key)), $value);
            } break;

            // Range
            case (is_object($value) && $value instanceof Range): {

                if ($value->getFrom()) {
                    $this->builder
                        ->andWhere(sprintf('%1$s.%2$s >= :%1$s_%3$s_from', $alias, $key, $this->createPlaceholder($key)))
                        ->setParameter(sprintf('%s_%s_from', $alias, $this->createPlaceholder($key)), $value->getFrom());
                }

                if ($value->getTo()) {
                    $this->builder
                        ->andWhere(sprintf('%1$s.%2$s <= :%1$s_%3$s_to', $alias, $key, $this->createPlaceholder($key)))
                        ->setParameter(sprintf('%s_%s_to', $alias, $this->createPlaceholder($key)), $value->getTo());
                }
            } break;

            // Operator "LIKE"
            case (is_object($value) && $value instanceof PartialMatchText): {
                $this->builder
                    ->andWhere(sprintf('%1$s.%2$s LIKE :%1$s_%3$s_value', $alias, $key, $this->createPlaceholder($key)))
                    ->setParameter(sprintf('%s_%s_value', $alias, $this->createPlaceholder($key)), '%' . $value->getText() . '%');
            } break;

            // Empty
            case (is_object($value) && $value instanceof EmptyData): {
                $this->builder
                    ->andWhere(sprintf('%1$s.%2$s IS NULL', $alias, $key));
            }
        }
    }

    /**
     * @param        $field
     * @param        $type
     * @param string $alias
     */
    protected function addOrderCondition($field, $type, $alias = self::ALIAS)
    {
        $this->builder->addOrderBy(sprintf('%s.%s', $alias, $field), $type);
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
            ->select(sprintf('COUNT(%s)', $builder->getRootAliases()[0]))
            ->setFirstResult(null)
            ->setMaxResults(null)
            ->getQuery()
            ->getSingleScalarResult();

        return $size;
    }
}
