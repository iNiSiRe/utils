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
        return strtoupper($value);
    }

    /**
     * @inheritdoc
     */
    public function reverseTransform($value)
    {
        return strtoupper($value);
    }
}
