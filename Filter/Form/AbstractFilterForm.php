<?php

namespace PrivateDev\Utils\Filter\Form;

use Symfony\Component\Form\AbstractType;

/**
 * Filter form type
 */
class AbstractFilterForm extends AbstractType
{
    public function getBlockPrefix()
    {
        return 'filter';
    }
}