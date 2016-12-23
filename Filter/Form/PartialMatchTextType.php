<?php

namespace PrivateDev\Utils\Filter\Form;

use PrivateDev\Utils\Filter\Model\PartialMatchText;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PartialMatchTextType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new CallbackTransformer(
            function ($object) {
                return is_null($object)
                    ? null
                    : $object->getText();
            },
            function ($string) {
                return is_null($string)
                    ? null
                    : (new PartialMatchText())->setText($string);
            }
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'compound' => false
        ]);
    }
}