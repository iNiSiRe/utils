<?php

namespace PrivateDev\Utils\Order\Form;

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
            'data_class' => CommentOrder::class,
            'method' => 'get'
        ]);
    }
}