<?php

namespace PrivateDev\Utils\Filter;

/**
 * Filter model
 */
interface FilterInterface
{
    public function getCollectionMaxSize() : int;

    public function getFilter() : array;
}