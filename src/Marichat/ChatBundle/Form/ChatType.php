<?php

namespace Marichat\ChatBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('message', 'text', array(
            'constraints' => array(new NotBlank()),
            'label' => false,
            'attr' => array('placeholder' => 'Message', 'class' => 'form-control')
        ));

        $builder->add('submit', 'submit', array(
            'label' => 'OK',
            'attr' => array('class' => 'btn btn-success glyphicon glyphicon-ok'),
        ));
    }

    public function getName()
    {
        return 'chat';
    }
}
