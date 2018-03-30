<?php

namespace PrivateDev\Utils\Filter\Form;

use PrivateDev\Utils\Filter\FilterData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterDataType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    FilterData::TYPE_SIMPLE_VALUE,
                    FilterData::TYPE_CONST
                ],
                'empty_data' => FilterData::TYPE_SIMPLE_VALUE
            ])
            ->add('condition', ChoiceType::class, [
                'choices' => [
                    FilterData::CONDITION_AND
                ],
                'empty_data' => (string) FilterData::CONDITION_AND
            ])
            ->add('operator', ChoiceType::class, [
                'choices' => [
                    FilterData::OPERATOR_EQUAL,
                    FilterData::OPERATOR_NOT_EQUAL
                ],
                'empty_data' => (string) FilterData::OPERATOR_EQUAL
            ])
            ->add('value', TextType::class);


        // For backward compatability
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {

            $data = $event->getData();

            if ($data === null || is_array($data)) {
                return;
            }

            if (is_string($data)) {
                $event->setData([
                    'value' => $data
                ]);
            }

        });
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', FilterData::class);
    }
}