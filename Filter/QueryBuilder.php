<?php

namespace PrivateDev\Utils\Filter;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use PrivateDev\Utils\Filter\Model\EmptyData;
use PrivateDev\Utils\Filter\Model\FilterInterface;
use PrivateDev\Utils\Filter\Model\Pagination;
use PrivateDev\Utils\Filter\Model\PartialMatchText;
use PrivateDev\Utils\Filter\Model\Range;
use Symfony\Component\Form\FormInterface;

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
    }

    /**
     * @param $key
     * @param $value
     */
    protected function addCondition($key, $value, $alias = self::ALIAS)
    {
        switch (true) {
            // String, numeric, bool
            case (is_string($value) || is_numeric($value) || is_bool($value)): {
                $this->builder
                    ->andWhere(sprintf('%1$s.%2$s = :%1$s_%2$s_value', $alias, $key))
                    ->setParameter(sprintf('%s_%s_value', $alias, $key), $value);
            } break;

            // DateTime
            case ($value instanceof \DateTime): {
                $this->builder
                    ->andWhere(sprintf('%1$s.%2$s = :%1$s_%2$s_value', $alias, $key))
                    ->setParameter(sprintf('%s_%s_value', $alias, $key), $value);
            } break;

            // Range
            case (is_object($value) && $value instanceof Range): {

                if ($value->getFrom()) {
                    $this->builder
                        ->andWhere(sprintf('%1$s.%2$s >= :%1$s_%2$s_from', $alias, $key))
                        ->setParameter(sprintf('%s_%s_from', $alias, $key), $value->getFrom());
                }

                if ($value->getTo()) {
                    $this->builder
                        ->andWhere(sprintf('%1$s.%2$s <= :%1$s_%2$s_to', $alias, $key))
                        ->setParameter(sprintf('%s_%s_to', $alias, $key), $value->getTo());
                }
            } break;

            // Operator "LIKE"
            case (is_object($value) && $value instanceof PartialMatchText): {
                $this->builder
                    ->andWhere(sprintf('%1$s.%2$s LIKE :%2$s_value', $alias, $key))
                    ->setParameter(sprintf('%s_value', $key), '%' . $value->getText() . '%');
            } break;

            // Empty
            case (is_object($value) && $value instanceof EmptyData): {
                $this->builder
                    ->andWhere(sprintf('%1$s.%2$s IS NULL', $alias, $key));
            }
        }
    }

    /**
     * @param FilterInterface $filter
     */
    protected function createQueryBuilder(FilterInterface $filter)
    {
        $this->builder = $this->repository->createQueryBuilder($filter->getRelationshipAlias());
    }

    /**
     * @param FilterInterface $filter
     *
     * @return $this
     */
    public function setFilter(FilterInterface $filter, $alias = self::ALIAS)
    {
        $this->createQueryBuilder($filter);

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
            ->select(sprintf('COUNT(%s)', $builder->getRootAliases()[0]))
            ->setFirstResult(null)
            ->setMaxResults(null)
            ->getQuery()
            ->getSingleScalarResult();

        return $size;
    }
}
