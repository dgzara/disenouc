<?php

namespace pDev\PracticasBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AlumnoPracticanteType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $width = '40px';
        $builder
            ->add('organizacion')
            ->add('supervisor')
            ->add('tipo', 'choice', array(
                'choices'   => array('Oficina' => 'Oficina', 'Servicio' => 'Servicio'),
                'required'  => false,
                'label' => 'Tipo de práctica',
                'label_attr' => array(
                    'data-help' => '- Servicio: está orientado a situar al estudiante en la realidad social, enfrentándolo a problemas complejos, donde desde el diseño aporte, con una postura ética, al impacto positivo en el desarrollo sustentable, el beneficio social y la mejora de la calidad de vida de las personas. 

- Oficina: está orientada a que el estudiante observe y comprenda desde la experiencia laboral, el valor del diseño en un mercado influenciado por variables de orden social, productivo, económico, ambiental cultural y político')
                                        ))
            ->add('comoContacto','choice',array(
                'label'=>'¿Cómo se contactó a la organización?',
                'choices' => array(
                    'Ofertas publicadas en este sitio' => 'Ofertas publicadas en este sitio',
                     'Contacto practicante anterior' => 'Contacto practicante anterior',
                     'Contacto propio' => 'Contacto propio',
                     'Contactado por empresa' => 'Contactado por empresa',
                     'Contactado por profesor' => 'Contactado por profesor',
                     'Otro' => 'Otro',
                ),
            ))
            ->add('ultimoTallerProfesor', null, array('label'=>'Profesor del taller'))
            ->add('ultimoTaller', 'choice', array(
                'label'=>'Último taller cursado',
                'choices'   => array(
                    '5. Calidad I' => '5. Calidad I',
                    '6. Calidad II' => '6. Calidad II',
                    '7. Mercado I' => '7. Mercado I',
                    '8. Mercado II' => '8. Mercado II',
                    '9. Seminario' => '9. Seminario',
                    '10. Título' => '10. Título'
                )
            ))
            ->add('fechaInicio', 'date', array(
                'widget' => 'single_text',                                            
                'format' => 'dd-MM-yyyy',                                            
                'invalid_message'=>'Valor no válido',
                'label' => 'Fecha de inicio',
                'attr' => array('placeholder' => 'dd-mm-aaaa')
            ))
            ->add('duracionCantidad', null, array(
                'label' => 'Duración',
            ))
            ->add('duracionUnidad', 'choice', array(
                'choices'   => array(
                    'días' => 'días', 
                    'semanas' => 'semanas',
                    'meses' => 'meses'),
            ))
            ->add('horasLunes',null,array('attr'=>array('placeholder'=>'Lunes','style'=>'width:'.$width)))
            ->add('horasMartes',null,array('attr'=>array('placeholder'=>'Martes','style'=>'width:'.$width)))
            ->add('horasMiercoles',null,array('attr'=>array('placeholder'=>'Miércoles','style'=>'width:'.$width)))
            ->add('horasJueves',null,array('attr'=>array('placeholder'=>'Jueves','style'=>'width:'.$width)))
            ->add('horasViernes',null,array('attr'=>array('placeholder'=>'Viernes','style'=>'width:'.$width)))
            ->add('horasSabado',null,array('attr'=>array('placeholder'=>'Sábado','style'=>'width:'.$width)))
            ->add('proyectos', 'collection', array('label'=>' ','type' => new ProyectoType(),'allow_add'=> true,'allow_delete' => true))
            ->add('desafios', 'collection', array('label'=>' ','type' => new DesafioType(),'allow_add'=> true,'allow_delete' => true))
        ;
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
        return 'pdev_practicasbundle_alumnopracticantetype';
    }
}
