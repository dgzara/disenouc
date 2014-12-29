<?php

namespace pDev\PracticasBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AlumnoPracticanteEstadoType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('estado', 'choice', array(
                'choices'   => array(
                    'estado.pendiente' => 'Pendiente', 
                    //'estado.enviada' => 'Enviada', 
                    'estado.aprobada' => 'Aprobada', 
                    'estado.rechazada' => 'Rechazada', 
                    //'estado.aceptada.alumno' => 'Aceptada por el alumno', 
                    //'estado.aceptada.supervisor' => 'Aceptada por la organización', 
                    //'estado.aceptada' => 'Aceptada por el alumno y la organización', 
                    //'estado.iniciada' => 'Iniciada', 
                    //'estado.terminada' => 'Terminada', 
                    //'estado.informe' => 'Informe entregado',
                    //'estado.evaluada' => 'Evaluada'
                ),
                'required'  => true,
            ))
            ->add('codigoPractica',null,array(
                'label'=>'Código practica',
                'label_attr' => array(
                    'data-help' => 'Sólo si se contactó a través de una publicación de este sistema.'
                )
            ))
            ->add('estadoObservaciones',null,array('label'=>'Observaciones'));
        
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'pDev\PracticasBundle\Entity\AlumnoPracticante'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'pdev_practicasbundle_alumnopracticanteestadotype';
    }
}
