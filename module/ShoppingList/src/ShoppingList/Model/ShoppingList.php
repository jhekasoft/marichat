<?php

namespace ShoppingList\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class ShoppingList
{
    protected $inputFilter;

    public $id;
    public $title;
    public $datetime;
    public $status;
    public $editing;
    public $editor_session_id;
    public $editing_datetime;
    
    public function isAnotherUserEditing()
    {
        if (session_id() == $this->editor_session_id) {
            return false;
        }
        
        if (!$this->editing) {
            return false;
        }
        
        $diffDateTime = new \DateTime();
        $editingDateTime = new \DateTime($this->editing_datetime);
        
        // Greater on 1 minute
        $diffDateTime->sub(new \DateInterval('PT1M'));
        if ($diffDateTime > $editingDateTime) {
            return false;
        }
        
        return true;
    }

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->title = (!empty($data['title'])) ? $data['title'] : null;
        $this->datetime = (!empty($data['datetime'])) ? $data['datetime'] : null;
        $this->status = (!empty($data['status'])) ? $data['status'] : null;
        $this->editing = (!empty($data['editing'])) ? $data['editing'] : null;
        $this->editor_session_id = (!empty($data['editor_session_id'])) ? $data['editor_session_id'] : null;
        $this->editing_datetime = (!empty($data['editing_datetime'])) ? $data['editing_datetime'] : null;
    }
    
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add(array(
                'name' => 'id',
                'required' => false,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));

            $inputFilter->add(array(
                'name' => 'title',
                'required' => true,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 100,
                        ),
                    ),
                ),
            ));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

}
