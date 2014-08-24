<?php

namespace ShoppingList\Form;

use Zend\Form\Form;

class ShoppingListForm extends Form
{

    public function __construct()
    {
        // we want to ignore the name passed
        parent::__construct('shopping-list');

        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'name' => 'title',
            'type' => 'Text',
            'options' => array(
                'label' => 'Title',
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
