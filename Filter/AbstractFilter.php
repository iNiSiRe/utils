<?php

namespace PrivateDev\Utils\Filter;

/**
 * Simple Filter with fields which are visible properties of class
 */
class AbstractFilter implements FilterInterface
{
    /**
     * @return int
     */
    protected function getCollectionMaxSize() : int
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
}