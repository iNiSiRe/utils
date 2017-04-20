<?php

namespace PrivateDev\Utils\Form;

use PrivateDev\Utils\Filter\Range;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Swagger\Annotations\Definition;
use Swagger\Annotations\Property;

/**
 * @Definition(
 *     definition="Filter/DateTimeRange",
 *     @Property(property="from", type="string", format="date-time"),
 *     @Property(property="to", type="string", format="date-time")
 * )
 */
class DateTimeRangeType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('from', DateTimeType::class, [
                'widget' => 'single_text',
                'format' => DateTimeType::HTML5_FORMAT
            ])
            ->add('to', DateTimeType::class, [
                'widget' => 'single_text',
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
}