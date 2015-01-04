<?php

namespace pDev\PracticasBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SupervisorOrganizacionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('rut',null,array('attr'=>array('autocomplete'=>'off')))
            ->add('nombres')
            ->add('apellidoPaterno')
            ->add('apellidoMaterno')
            ->add('email')            
            ->add('cargo')
            ->add('profesion');
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'pDev\PracticasBundle\Entity\Supervisor'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'pdev_practicasbundle_supervisortype';
    }
}
