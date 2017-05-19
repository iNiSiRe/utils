<?php

namespace PrivateDev\Utils\Order\Form;

use PrivateDev\Utils\Order\DefaultOrder;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DefaultOrderType extends AbstractOrderForm
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id', OrderType::class);
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => DefaultOrder::class,
            'method' => 'get'
        ]);
    }
}