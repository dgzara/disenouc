<?php

namespace pDev\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserBasicType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('rut',null,array('required'=>true))
                ->add('nombres')                
                ->add('apellidoPaterno')                
                ->add('apellidoMaterno')
                ->add('numeroTelefono',null,array('label'=>'Número de teléfono'))
                ->add('direccionCalle',null,array('label'=>'Dirección'))                
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'pDev\UserBundle\Entity\User'
        ));
    }

    public function getName()
    {
        return 'pdev_userbundle_userbasictype';
    }
}
