<?php

namespace pDev\PracticasBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PracticaEstadoType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('estado', 'choice', array(
                                        'choices'   => array('estado.pendiente' => 'Pendiente', 'estado.aprobada' => 'Aprobado', 'estado.rechazada' => 'Rechazado', 'estado.publicada' => 'Publicado'),
                                        'required'  => true,
                                        ))
            ->add('estadoObservaciones',null,array('label'=>'Observaciones'))
             ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'pDev\PracticasBundle\Entity\Practica'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'pdev_practicasbundle_practicaestadotype';
    }
}
