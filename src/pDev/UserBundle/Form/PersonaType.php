<?php

namespace pDev\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PersonaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombres',null,array('label'=>'Nombres','required'=>true))
            ->add('apellidoPaterno',null,array('label'=>'Apellido paterno','required'=>true))
            ->add('apellidoMaterno',null,array('label'=>'Apellido materno','required'=>true))
            ->add('email',null,array('label'=>'Email UC','required'=>true))
            ->add('emailSecundario',null,array('label'=>'Email alternativo','required'=>false))
            ->add('rut',null,array('label'=>'RUT (sin puntos ni guion)','required'=>true))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'pDev\UserBundle\Entity\Persona'
        ));
    }

    public function getName()
    {
        return 'pdev_userbundle_personatype';
    }
}
