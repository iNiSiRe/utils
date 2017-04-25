<?php

namespace PrivateDev\Utils\Filter;

use Symfony\Component\Validator\Constraints as Assert;

class Pagination
{
    /**
     * @Assert\Range(min="1", max="250")
     *
     * @var int
     */
    protected $limit = 250;

    /**
     * @Assert\Range(min="0")
     *
     * @var int
     */
    protected $offset = 0;

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     *
     * @return $this
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @param int $offset
     *
     * @return $this
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;

        return $this;
    }
}