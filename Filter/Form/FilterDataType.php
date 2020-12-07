<?php

namespace PrivateDev\Utils\Filter\Form;

use PrivateDev\Utils\Filter\FilterData;
use Symfony\Component\Form\AbstractType;
use PrivateDev\Utils\Form\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class FilterDataType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices'    => [
                    FilterData::TYPE_SIMPLE_VALUE,
                    FilterData::TYPE_CONST,
                    FilterData::TYPE_ARRAY_VALUE,
                ],
                'empty_data' => (string) FilterData::TYPE_SIMPLE_VALUE,
            ])
            ->add('condition', ChoiceType::class, [
                'choices'    => [
                    FilterData::CONDITION_AND,
                    FilterData::CONDITION_IN,
                ],
                'empty_data' => (string) FilterData::CONDITION_AND,
            ])
            ->add('operator', ChoiceType::class, [
                'choices'    => [
                    FilterData::OPERATOR_EQUAL,
                    FilterData::OPERATOR_NOT_EQUAL,
                    FilterData::OPERATOR_GREATER_THAN,
                    FilterData::OPERATOR_GREATER_THAN_OR_EQUAL,
                    FilterData::OPERATOR_LESS_THAN,
                    FilterData::OPERATOR_LESS_THAN_OR_EQUAL,
                ],
                'empty_data' => (string) FilterData::OPERATOR_EQUAL,
            ]);

        $builder->get('type')->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {

            $data = $event->getForm()->getData();

            $options = [];
            switch ($data) {
                case FilterData::TYPE_CONST:
                    $type = NumberType::class;
                    break;

                case FilterData::TYPE_ARRAY_VALUE:
                    $type = CollectionType::class;
                    $options = [
                        'allow_add' => true,
                        'entry_type' => $event->getForm()->getParent()->getConfig()->getOption('entry_type')
                    ];
                    break;

                default:
                    $type = $event->getForm()->getParent()->getConfig()->getOption('entry_type');
                    break;
            }

            $event->getForm()->getParent()->add('value', $type, $options);

        });

        // For backward compatability
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {

            $data = $event->getData();

            if ($data === null || is_array($data)) {
                return;
            }

            if (is_string($data)) {
                $event->setData([
                    'value' => $data,
                ]);
            }

        });
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FilterData::class,
            'entry_type' => TextType::class,
        ]);
    }
}