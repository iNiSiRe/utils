<?php

namespace PrivateDev\Utils\Filter;

use Doctrine\ORM\EntityManager;
use PrivateDev\Utils\Filter\Query\QueryBuilder;

class QueryBuilderFactory
{
    /**
     * @var EntityManager
     */
    private $manager;

    /**
     * QueryBuilderFactory constructor.
     *
     * @param EntityManager $manager
     */
    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param string $entityClass
     *
     * @return QueryBuilder
     */
    public function create(string $entityClass)
    {
        return new QueryBuilder($this->manager->getRepository($entityClass));
    }
}