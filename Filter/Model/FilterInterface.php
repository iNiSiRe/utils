<?php

namespace PrivateDev\Utils\Filter\Model;

/**
 * Filter model
 */
interface FilterInterface
{
    public function getCollectionMaxSize() : int;

    public function getFilter() : array;

    public function getRelationshipAlias();
}