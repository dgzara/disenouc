<?php

namespace pDev\PracticasBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProyectoTaskType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre')
            ->add('fechaInicio', 'date',array('widget' => 'single_text',                                            
                                            'format' => 'dd-MM-yyyy',                                            
                                            'invalid_message'=>'Valor no válido',
                                            'label' => 'Fecha de inicio',
                                            'attr' => array('placeholder' => 'dd-mm-aaaa')
                                            ))
            ->add('fechaTermino', 'date',array('widget' => 'single_text',                                            
                                            'format' => 'dd-MM-yyyy',                                            
                                            'invalid_message'=>'Valor no válido',
                                            'label' => 'Fecha de término',
                                            'attr' => array('placeholder' => 'dd-mm-aaaa')
                                            ))
            ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'pDev\PracticasBundle\Entity\ProyectoTask'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'pdev_practicasbundle_proyectotasktype';
    }
}
