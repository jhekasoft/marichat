<?php

namespace Marichat\ChatBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ChatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('message', 'text', array('label' => false, 'attr' => array('placeholder' => 'Message', 'class' => 'form-control')));
    }

    public function getName()
    {
        return 'chat';
    }
}
