<?php

namespace PrivateDev\Utils\Filter\Model;

use Doctrine\ORM\Query\Expr\Join;

class AbstractDeepFilter extends AbstractFilter
{
    /**
     * @return Join[]
     */
    public function getJoins()
    {
        return [];
    }
}