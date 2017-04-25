<?php

namespace PrivateDev\Utils\Filter\Form;

use Symfony\Component\Form\AbstractType;

/**
 * Filter form type
 */
abstract class AbstractFilterType extends AbstractType
{
    public function getBlockPrefix()
    {
        return 'filter';
    }
}