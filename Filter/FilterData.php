<?php
/**
 * Created by PhpStorm.
 * User: inisire
 * Date: 23.03.18
 * Time: 13:34
 */

namespace PrivateDev\Utils\Filter;


class FilterData
{
    const TYPE_SIMPLE_VALUE = 1;
    const TYPE_CONST = 2;

    const CONDITION_AND = 1;
    const CONDITION_OR = 2;

    const OPERATOR_EQUAL = 1;
    const OPERATOR_NOT_EQUAL = 2;

    const CONST_VALUE_NULL = 1;

    /**
     * @var int
     */
    private $type = self::TYPE_SIMPLE_VALUE;

    /**
     * @var int
     */
    private $condition = self::CONDITION_AND;

    /**
     * @var int
     */
    private $operator = self::OPERATOR_EQUAL;

    /**
     * @var string
     */
    private $value;

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param int $type
     *
     * @return FilterData
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     *
     * @return FilterData
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return int
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * @param int $operator
     *
     * @return FilterData
     */
    public function setOperator($operator)
    {
        $this->operator = $operator;

        return $this;
    }

    /**
     * @return int
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * @param int $condition
     *
     * @return FilterData
     */
    public function setCondition($condition)
    {
        $this->condition = $condition;

        return $this;
    }
}