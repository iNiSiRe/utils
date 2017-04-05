<?php

namespace PrivateDev\Utils\Builder;

use PrivateDev\Utils\Filter\Model\EmptyData;
use PrivateDev\Utils\Filter\Model\PartialMatchText;
use PrivateDev\Utils\Filter\Model\Range;
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
            $this->addQuery($value, $key);
            return;
        }

        switch (true) {
            // String, numeric, bool
            case (is_string($value) || is_numeric($value) || is_bool($value)): {
                $this->builder
                    ->andWhere(sprintf('%1$s.%2$s = :%1$s_%3$s_value', $alias, $key, $this->createPlaceholder($key)))
                    ->setParameter(sprintf('%s_%s_value', $alias, $this->createPlaceholder($key)), $value);
            } break;

            // DateTime
            case ($value instanceof \DateTime): {
                $this->builder
                    ->andWhere(sprintf('%1$s.%2$s = :%1$s_%3$s_value', $alias, $key, $this->createPlaceholder($key)))
                    ->setParameter(sprintf('%s_%s_value', $alias, $this->createPlaceholder($key)), $value);
            } break;

            // Range
            case (is_object($value) && $value instanceof Range): {

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
            } break;

            // Operator "LIKE"
            case (is_object($value) && $value instanceof PartialMatchText): {
                $this->builder
                    ->andWhere(sprintf('%1$s.%2$s LIKE :%1$s_%3$s_value', $alias, $key, $this->createPlaceholder($key)))
                    ->setParameter(sprintf('%s_%s_value', $alias, $this->createPlaceholder($key)), '%' . $value->getText() . '%');
            } break;

            // Empty
            case (is_object($value) && $value instanceof EmptyData): {
                $this->builder
                    ->andWhere(sprintf('%1$s.%2$s IS NULL', $alias, $key));
            }

            // ArrayCollection
            case (is_object($value) && $value instanceof ArrayCollection): {
                $this->builder
                    ->andWhere(sprintf('%1$s.%2$s IN (:%1$s_%3$s_value)', $alias, $key, $this->createPlaceholder($key)))
                    ->setParameter(sprintf('%s_%s_value', $alias, $this->createPlaceholder($key)), $value);
            } break;
        }
    }
}
