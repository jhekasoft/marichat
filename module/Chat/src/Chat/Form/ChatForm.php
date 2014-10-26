<?php

namespace Chat\Form;

use Zend\Form\Form;

class ChatForm extends Form
{

    public function __construct()
    {
        // we want to ignore the name passed
        parent::__construct('chat');

        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'text',
            'type' => 'Text',
            'options' => array(
                'label' => 'Text',
            ),
            'attributes' => array(
                'class' => 'form-control',
            ),
        ));
    }

    public function reset()
    {
        $elements = $this->getElements();

        foreach ($elements as $element) {
            if ($element instanceof \Zend\Form\Element\Text) {
                $element->setValue('');
            }
        }
    }

}
