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
                'choices' => array(
                    'estado.pendiente' => 'Pendiente', 
                    'estado.aprobada' => 'Aprobar', 
                    'estado.rechazada' => 'Rechazar', 
                ),
                'required'  => true,
            ))
            ->add('tipo', 'choice', array(
                'choices'   => array('Oficina' => 'Oficina', 'Servicio' => 'Servicio'),
                'required'  => false,
                'label_attr' => array('data-help' => '- Servicio: está orientado a situar al estudiante en la realidad social, enfrentándolo a problemas complejos, donde desde el diseño aporte, con una postura ética, al impacto positivo en el desarrollo sustentable, el beneficio social y la mejora de la calidad de vida de las personas. 

- Oficina: está orientada a que el estudiante observe y comprenda desde la experiencia laboral, el valor del diseño en un mercado influenciado por variables de orden social, productivo, económico, ambiental cultural y político')
                                        ))
            ->add('estadoObservaciones',null,array('label'=>'Observaciones'));
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
