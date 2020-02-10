<?php

namespace PrivateDev\Utils\Form;

use PrivateDev\Utils\Filter\Range;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DateTimeRangeType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('from', \PrivateDev\Utils\Form\DateTimeType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'format' => DateTimeType::HTML5_FORMAT
            ])
            ->add('to', \PrivateDev\Utils\Form\DateTimeType::class, [
                'widget' => 'single_text',
                'html5' => false,
                'format' => DateTimeType::HTML5_FORMAT
            ]);
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Range::class
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'period';
    }
}