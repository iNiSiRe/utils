<?php

namespace PrivateDev\Utils\Order\Form\Transformer;

use Symfony\Component\Form\DataTransformerInterface;

class OrderTransformer implements DataTransformerInterface
{
    /**
     * @inheritdoc
     */
    public function transform($value)
    {
        return !is_null($value) ? strtoupper($value) : $value;
    }

    /**
     * @inheritdoc
     */
    public function reverseTransform($value)
    {
        return !is_null($value) ? strtoupper($value) : $value;
    }
}
