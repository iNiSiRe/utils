<?php

namespace PrivateDev\Utils\Order\Form;

use Symfony\Component\Form\AbstractType;

/**
 * Order form type
 */
class AbstractOrderForm extends AbstractType
{
    public function getBlockPrefix()
    {
        return 'order';
    }
}
