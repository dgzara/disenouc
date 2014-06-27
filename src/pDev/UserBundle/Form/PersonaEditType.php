<?php

namespace pDev\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PersonaEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('rut',null,array('label'=>'Rut','required'=>true))
            ->add('nombres',null,array('label'=>'Nombres'))
            ->add('apellidoPaterno',null,array('label'=>'Apellido paterno'))
            ->add('apellidoMaterno',null,array('label'=>'Apellido materno'))
            ->add('email',null,array('label'=>'Correo electrónico'))
            ->add('emailSecundario',null,array('label'=>'Correo electrónico secundario'))
            
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
        return 'pdev_userbundle_personaedittype';
    }
}
