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
    public function transform($object)
    {
        return [$this->getName() => $this->doTransform($object)];
    }

    /**
     * @param object $object
     *
     * @return array
     */
    abstract protected function doTransform($object) : array;

    /**
     * @return string
     */
    abstract protected function getName() : string;
}