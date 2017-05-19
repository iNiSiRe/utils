<?php

namespace PrivateDev\Utils\Order;

use Doctrine\Common\Collections\Criteria;
use PrivateDev\Utils\Builder\Query\AbstractQuery;

class DefaultOrder extends AbstractQuery
{
    /**
     * @var string
     */
    protected $id = Criteria::ASC;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}