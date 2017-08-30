<?php

namespace PrivateDev\Utils\Form\Transformer;

use Symfony\Component\Form\DataTransformerInterface;

class BooleanTransformer implements DataTransformerInterface
{
    /**
     * @inheritdoc
     */
    public function transform($value)
    {
        return $value === true ? 1 : 0;
    }

    /**
     * @inheritdoc
     */
    public function reverseTransform($value)
    {
        if ($value === 'true' || $value === '1' || $value === 1 || $value === true) {
            return true;
        } elseif ($value === 'false' || $value === false || $value === 0 || $value === '0') {
            return false;
        }

        return null;
    }
}