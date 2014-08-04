<?php

namespace pDev\UserBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

class RegistrationFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        // add your custom field
        $builder->add('rut',null,array('required'=>true))
            ->add('nombres')                
            ->add('apellidoPaterno')                
            ->add('apellidoMaterno')
            ->add('numeroTelefono',null,array('label'=>'Número de teléfono'))
            ->add('direccionCalle',null,array('label'=>'Dirección'))   
        ;
    }

    public function getName()
    {
        return 'pdev_user_registration';
    }
}
