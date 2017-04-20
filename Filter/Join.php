<?php

namespace PrivateDev\Utils\Filter;

class Join
{
    /**
     * @var string
     */
    private $target;

    /**
     * @var string
     */
    private $condition;

    /**
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @param string $target
     */
    public function setTarget(string $target)
    {
        $this->target = $target;
    }

    /**
     * @return string
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * @param string $condition
     */
    public function setCondition(string $condition)
    {
        $this->condition = $condition;
    }
}