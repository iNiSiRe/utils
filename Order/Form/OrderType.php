<?php

namespace PrivateDev\Utils\Order\Form;

use PrivateDev\Utils\Order\Form\Transformer\OrderTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class OrderType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new OrderTransformer());
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'compound' => false,
            'constraints' => new Callback(function($object, ExecutionContextInterface $context) {
                if ($object !== 'DESC' && $object !== 'ASC' && $object !== "") {
                    $context->addViolation('The value you selected is not a valid choice.');
                }
            })
        ]);
    }
}
