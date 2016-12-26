<?php

namespace PrivateDev\Utils\Filter\Model;

class Range
{
    /**
     * @var mixed
     */
    protected $from;

    /**
     * @var mixed
     */
    protected $to;

    /**
     * @return mixed
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @param mixed $from
     *
     * @return Range
     */
    public function setFrom($from)
    {
        $this->from = $from;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @param mixed $to
     *
     * @return Range
     */
    public function setTo($to)
    {
        $this->to = $to;

        return $this;
    }
}