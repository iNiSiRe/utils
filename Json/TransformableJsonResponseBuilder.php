<?php

namespace PrivateDev\Utils\Json;

use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\ResourceInterface;
use PrivateDev\Utils\Fractal\TransformerAbstract;

class TransformableJsonResponseBuilder extends JsonResponseBuilder
{
    /**
     * @param ResourceInterface $resource
     *
     * @return TransformableJsonResponseBuilder
     */
    private function setTransformableResource(ResourceInterface $resource)
    {
        $transformed = $this->fractal
            ->createData($resource)
            ->toArray();

        $this->setData('data', $transformed['data']);

        if (isset($transformed['included'])) {
            $this->setData('included', $transformed['included']);
        }

        return $this;
    }

    /**
     * @param $object
     * @param $transformer
     *
     * @return TransformableJsonResponseBuilder
     */
    public function setTranformableItem($object, TransformerAbstract $transformer)
    {
        return $this->setTransformableResource(new Item($object, $transformer, $transformer->getResourceKey()));
    }

    /**
     * @param $collection
     * @param $transformer
     *
     * @return TransformableJsonResponseBuilder
     */
    public function setTransformableCollection($collection, TransformerAbstract $transformer)
    {
        return $this->setTransformableResource(new Collection($collection, $transformer, $transformer->getResourceKey()));
    }
}