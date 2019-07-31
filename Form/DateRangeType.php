<?php

namespace PrivateDev\Utils\Form;

use StatisticBundle\Model\DateRange;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Swagger\Annotations\Definition;
use Swagger\Annotations\Property;

class DateRangeType extends DateTimeRangeType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            [$this, 'onPostSubmit']
        );
    }


    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'method' => 'get',
            'data_class' => DateRange::class
        ]);
    }

    /**
     * @param FormEvent $formEvent
     */
    public function onPostSubmit(FormEvent $formEvent)
    {
        if (!$formEvent->getForm()->isValid()) {
            return;
        }

        /** @var DateRange $period */
        $period = $formEvent->getData();

        $period->getFrom()->setTime(0, 0, 0);
        $period->getTo()->setTime(23, 59, 59);
    }
}
