<?php

namespace pDev\UserBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use pDev\UserBundle\Form\PersonaType as BaseType;

class FuncionarioType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
    }

    public function getName()
    {
        return 'pdev_user_registration';
    }
}
