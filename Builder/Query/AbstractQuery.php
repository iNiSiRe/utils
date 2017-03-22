<?php

namespace PrivateDev\Utils\Filter\Model;

/**
 * Simple Filter with fields which are visible properties of class
 */
abstract class AbstractFilter implements FilterInterface
{
    /**
     * @return int
     */
    public function getCollectionMaxSize() : int
    {
        return 100;
    }

    /**
     * @return array
     */
    public function getFilter() : array
    {
        return get_object_vars($this);
    }

    //TODO
    /**
     * @return string
     */
    public function getRelationshipAlias()
    {
        return 'a';
    }

    /**
     * @inheritdoc
     */
    public function getJoins()
    {
        return [];
    }
}