<?php

namespace PrivateDev\Utils\Filter\Model;

use Doctrine\ORM\Query\Expr\Join;

interface DeepFilterInterface extends FilterInterface
{
    /**
     * @return Join[]
     */
    public function getJoins();
}