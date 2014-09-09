<?php

namespace pDev\UserBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use pDev\UserBundle\Form\PersonaType as BaseType;

class AlumnoType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        // add your custom field
        $builder->add('numeroalumno', null, array(
            'label' => 'Número de alumno',
            'required'=>true
        ));
    }

    public function getName()
    {
        return 'pdev_user_registration';
    }
}
