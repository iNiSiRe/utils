<?php


namespace PrivateDev\Utils\Form;


use PrivateDev\Utils\Form\Transformer\DateTimeToRfc3339Transformer;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeImmutableToDateTimeTransformer;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToTimestampTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\ReversedTransformer;

class DateTimeType extends \Symfony\Component\Form\Extension\Core\Type\DateTimeType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $pattern = \is_string($options['format']) ? $options['format'] : null;

        if (self::HTML5_FORMAT === $pattern) {
            $builder->addViewTransformer(new DateTimeToRfc3339Transformer(
                $options['model_timezone'],
                $options['view_timezone']
            ));
        }

        if ('datetime_immutable' === $options['input']) {
            $builder->addModelTransformer(new DateTimeImmutableToDateTimeTransformer());
        } elseif ('string' === $options['input']) {
            $builder->addModelTransformer(new ReversedTransformer(
                new DateTimeToStringTransformer($options['model_timezone'], $options['model_timezone'], $options['input_format'])
            ));
        } elseif ('timestamp' === $options['input']) {
            $builder->addModelTransformer(new ReversedTransformer(
                new DateTimeToTimestampTransformer($options['model_timezone'], $options['model_timezone'])
            ));
        }
    }
}