<?php

namespace PrivateDev\Utils\Builder\Query;

use Doctrine\ORM\Query\Expr\Join;

interface QueryInterface
{
    /**
     * @return array
     */
    public function getQuery();

    /**
     * @return Join[]
     */
    public function getJoins();
}
