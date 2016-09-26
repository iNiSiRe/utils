<?php

namespace PrivateDev\Utils\Fractal;

use League\Fractal\TransformerAbstract as BaseTransformerAbstract;

abstract class TransformerAbstract extends BaseTransformerAbstract
{
    /**
     * @param object $object
     *
     * @return array
     */
    abstract public function transform($object) : array;

    /**
     * @return string
     */
    abstract public function getResourceKey() : string;

    /**
     * @param mixed               $data
     * @param TransformerAbstract $transformer
     * @param string              $resourceKey
     *
     * @return \League\Fractal\Resource\Item
     */
    protected function item($data, $transformer, $resourceKey = null)
    {
        $resourceKey = $resourceKey === null
            ? $transformer->getResourceKey()
            : $resourceKey;

        return parent::item($data, $transformer, $resourceKey);
    }

    /**
     * @param mixed               $data
     * @param TransformerAbstract $transformer
     * @param null                $resourceKey
     *
     * @return \League\Fractal\Resource\Collection
     */
    protected function collection($data, $transformer, $resourceKey = null)
    {
        $resourceKey = $resourceKey === null
            ? $transformer->getResourceKey()
            : $resourceKey;

        return parent::collection($data, $transformer, $resourceKey);
    }
}