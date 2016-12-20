<?php

namespace PrivateDev\Utils\Filter\Form;

use Symfony\Component\Form\AbstractType;

/**
 * Filter form type
 */
abstract class AbstractFilterForm extends AbstractType
{
    public function getBlockPrefix()
    {
        return 'filter';
    }
}