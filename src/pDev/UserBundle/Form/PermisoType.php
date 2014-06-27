<?php

namespace pDev\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PermisoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('role', 'entity', array(
                                    'class' => 'pDevUserBundle:Role',
                                    'label'=>false,
                                    'multiple'  => false,
                        ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'pDev\UserBundle\Entity\Permiso'
        ));
    }

    public function getName()
    {
        return 'pdev_userbundle_permisotype';
    }
}
