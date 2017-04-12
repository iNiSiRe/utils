<?php

namespace PrivateDev\Utils\Filter\Model;

use PrivateDev\Utils\Builder\Query\AbstractQuery;

class Filter extends AbstractQuery
{
    /**
     * @var array
     */
    private $filter = [];

    /**
     * Filter constructor.
     *
     * @param array $filter
     */
    public function __construct($filter = [])
    {
        $this->filter = $filter;
    }

    /**
     * @inheritDoc
     */
    public function getQuery()
    {
        return $this->filter;
    }
}