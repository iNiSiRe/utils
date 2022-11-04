<?php

namespace PrivateDev\Utils\Form\EventListener;

use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Extension\Core\EventListener\ResizeFormListener;
use Symfony\Component\Form\FormEvent;

class VariedTypeResizeFormListener extends ResizeFormListener
{
    private $closure;

    public function __construct($closure, $type, array $options = [], $allowAdd = false, $allowDelete = false, $deleteEmpty = false)
    {
        parent::__construct($type, $options, $allowAdd, $allowDelete, $deleteEmpty);

        $this->closure = $closure;
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        if (null === $data) {
            $data = array();
        }

        if (!is_array($data) && !($data instanceof \Traversable && $data instanceof \ArrayAccess)) {
            throw new UnexpectedTypeException($data, 'array or (\Traversable and \ArrayAccess)');
        }

        // First remove all rows
        foreach ($form as $name => $child) {
            $form->remove($name);
        }

        // Then add all rows again in the correct order
        foreach ($data as $name => $value) {
            $discriminatorForm = null;

            $form->add($name, call_user_func_array($this->closure, [$value, &$discriminatorForm]), array_replace(array(
                'property_path' => '['.$name.']',
            ), $this->options));

            $form->get($name)->add($discriminatorForm);
        }
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        if (!is_array($data) && !($data instanceof \Traversable && $data instanceof \ArrayAccess)) {
            $data = array();
        }

        // Remove all empty rows
        if ($this->allowDelete) {
            foreach ($form as $name => $child) {
                if (!isset($data[$name])) {
                    $form->remove($name);
                }
            }
        }

        // Add all additional rows
        if ($this->allowAdd) {
            foreach ($data as $name => $value) {
                if (!$form->has($name)) {
                    $discriminatorForm = null;

                    $form->add($name, call_user_func_array($this->closure, [$value, &$discriminatorForm]), array_replace(array(
                        'property_path' => '['.$name.']',
                    ), $this->options));

                    $form->get($name)->add($discriminatorForm);
                }
            }
        }

        $event->setData($data);
    }
}
