<?php

namespace PrivateDev\Utils\Builder\Query;

use Doctrine\ORM\Query\Expr\Join;

abstract class AbstractQuery implements QueryInterface
{
    /**
     * @return array
     */
    public function getQuery()
    {
        return get_object_vars($this);
    }

    /**
     * @return Join[]
     */
    public function getJoins()
    {
        return [];
    }
}
