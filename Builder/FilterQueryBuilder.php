<?php

namespace PrivateDev\Utils\Builder;

use Doctrine\Common\Collections\ArrayCollection;
use PrivateDev\Utils\Filter\EmptyData;
use PrivateDev\Utils\Filter\FilterData;
use PrivateDev\Utils\Filter\PartialMatchText;
use PrivateDev\Utils\Filter\Range;
use PrivateDev\Utils\Builder\Query\QueryInterface;

class FilterQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @param        $key
     * @param        $value
     * @param string $alias
     */
    protected function addCondition($key, $value, $alias)
    {
        if (is_object($value) && $value instanceof QueryInterface) {
            $this->addQuery($value, "{$alias}.{$key}");
            return;
        }

        switch (true) {
            // String, numeric, bool
            case (is_string($value) || is_numeric($value) || is_bool($value)):
                {
                    $this->builder
                        ->andWhere(sprintf('%1$s.%2$s = :%1$s_%3$s_value', $alias, $key, $this->createPlaceholder($key)))
                        ->setParameter(sprintf('%s_%s_value', $alias, $this->createPlaceholder($key)), $value);
                }
                break;

            // DateTime
            case ($value instanceof \DateTime):
                {
                    $this->builder
                        ->andWhere(sprintf('%1$s.%2$s = :%1$s_%3$s_value', $alias, $key, $this->createPlaceholder($key)))
                        ->setParameter(sprintf('%s_%s_value', $alias, $this->createPlaceholder($key)), $value);
                }
                break;

            // Range
            case (is_object($value) && $value instanceof Range):
                {

                    if ($value->getFrom()) {
                        $this->builder
                            ->andWhere(sprintf('%1$s.%2$s >= :%1$s_%3$s_from', $alias, $key, $this->createPlaceholder($key)))
                            ->setParameter(sprintf('%s_%s_from', $alias, $this->createPlaceholder($key)), $value->getFrom());
                    }

                    if ($value->getTo()) {
                        $this->builder
                            ->andWhere(sprintf('%1$s.%2$s <= :%1$s_%3$s_to', $alias, $key, $this->createPlaceholder($key)))
                            ->setParameter(sprintf('%s_%s_to', $alias, $this->createPlaceholder($key)), $value->getTo());
                    }
                }
                break;

            // Operator "LIKE"
            case (is_object($value) && $value instanceof PartialMatchText):
                {
                    $this->builder
                        ->andWhere(sprintf('%1$s.%2$s LIKE :%1$s_%3$s_value', $alias, $key, $this->createPlaceholder($key)))
                        ->setParameter(sprintf('%s_%s_value', $alias, $this->createPlaceholder($key)), '%' . $value->getText() . '%');
                }
                break;

            // Empty
            case (is_object($value) && $value instanceof EmptyData):
                {
                    $this->builder
                        ->andWhere(sprintf('%1$s.%2$s IS NULL', $alias, $key));
                }
                break;

            // ArrayCollection
            case (is_object($value) && $value instanceof ArrayCollection):
                {
                    $this->builder
                        ->andWhere(sprintf('%1$s.%2$s IN (:%1$s_%3$s_value)', $alias, $key, $this->createPlaceholder($key)))
                        ->setParameter(sprintf('%s_%s_value', $alias, $this->createPlaceholder($key)), $value);
                }
                break;

            // FilterData
            case (is_object($value) && $value instanceof FilterData && !is_null($value->getValue())):
                {
                    $operand1 = "{$alias}.{$key}";

                    if ($value->getType() == FilterData::TYPE_CONST && $value->getValue() == FilterData::CONST_VALUE_NULL) {
                        $operator = $value->getOperator() == FilterData::OPERATOR_EQUAL
                            ? 'IS'
                            : 'IS NOT';

                        $operand2 = 'NULL';

                        $this->builder->andWhere("{$operand1} {$operator} {$operand2}");
                    } elseif ($value->getType() == FilterData::TYPE_ARRAY_VALUE && $value->getCondition() == FilterData::CONDITION_IN) {
                        if (!is_array($value->getValue())) {
                            return;
                        }

                        $operator = $value->getOperator() == FilterData::OPERATOR_EQUAL
                            ? 'IN'
                            : 'NOT IN';

                        $operand2 = "{$alias}_{$this->createPlaceholder($key)}_value";

                        $this->builder
                            ->andWhere("{$operand1} {$operator} (:{$operand2})")
                            ->setParameter($operand2, $value->getValue());
                    } else {
                        $operator = $value->getOperator() == FilterData::OPERATOR_EQUAL
                            ? '='
                            : '!=';

                        $operand2 = "{$alias}_{$this->createPlaceholder($key)}_value";

                        $this->builder
                            ->andWhere("{$operand1} {$operator} :{$operand2}")
                            ->setParameter($operand2, $value->getValue());
                    }
                }
                break;
        }
    }
}
