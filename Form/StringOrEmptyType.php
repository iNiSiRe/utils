<?php

namespace PrivateDev\Utils\Form;

use PrivateDev\Utils\Filter\EmptyData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StringOrEmptyType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addViewTransformer(new CallbackTransformer(
            function ($data) {
                return $data instanceof EmptyData ? "" : $data;
            },
            function ($data) {
                return $data === "" ? new EmptyData() : $data;
            }
        ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'compound' => false,
            'empty_data' => function ($form, $data) {
                return $data;
            }
        ]);
    }
}
