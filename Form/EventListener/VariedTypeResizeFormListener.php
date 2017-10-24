<?php

namespace PrivateDev\Utils\Form\EventListener;

use Symfony\Component\Form\Extension\Core\EventListener\ResizeFormListener;
use Symfony\Component\Form\FormEvent;

class VariedTypeResizeFormListener extends ResizeFormListener
{
    private $closure;

    /**
     * VariedTypeResizeFormListener constructor.
     *
     * @param       $type
     * @param array $options
     * @param bool  $allowAdd
     * @param bool  $allowDelete
     * @param bool  $deleteEmpty
     * @param       $closure
     */
    public function __construct($type, array $options = [], $allowAdd = false, $allowDelete = false, $deleteEmpty = false, $closure)
    {
        parent::__construct($type, $options, $allowAdd, $allowDelete, $deleteEmpty);

        $this->closure = $closure;
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
