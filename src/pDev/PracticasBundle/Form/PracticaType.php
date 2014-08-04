<?php

namespace pDev\PracticasBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PracticaType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('organizacionAlias')
            ->add('contacto')
            ->add('tipo', 'choice', array(
                'choices'   => array('Oficina' => 'Oficina', 'Servicio' => 'Servicio'),
                'required'  => false,
                'label_attr' => array('data-help' => '- Servicio: está orientado a situar al estudiante en la realidad social, enfrentándolo a problemas complejos, donde desde el diseño aporte, con una postura ética, al impacto positivo en el desarrollo sustentable, el beneficio social y la mejora de la calidad de vida de las personas. 

- Oficina: está orientada a que el estudiante observe y comprenda desde la experiencia laboral, el valor del diseño en un mercado influenciado por variables de orden social, productivo, económico, ambiental cultural y político')
                                        ))           
            ->add('jornadas', 'choice', array(
                'choices'   => array(
                    'Part-time' => 'Part-time', 
                    'Full-time' => 'Full-time'),
                'required'  => false,
                'label_attr' => array('data-help' => '240 horas')
            ))
            ->add('fechaInicio', 'date', array('widget' => 'single_text',                                            
                'invalid_message'=>'Valor no válido',
                'label' => 'Fecha de inicio',
                'format' => 'dd-MM-yyyy',
                'attr' => array('placeholder' => 'dd-mm-aaaa')
            ))
            ->add('fechaTermino', 'date', array('widget' => 'single_text',                                            
                'invalid_message'=>'Valor no válido',
                'label' => 'Fecha de término',
                'format' => 'dd-MM-yyyy',
                'attr' => array('placeholder' => 'dd-mm-aaaa')
            ))
            ->add('manejoSoftware',null,array('label' => 'Manejo de software','label_attr' => array('data-help' => 'ej.Adobe Photoshop, Topsolid, Rhino, Illustrator, etc')))
            ->add('interes',null,array('label' => 'Interés','label_attr' => array('data-help' => 'ej.Industrial, gráfico, ambos, multimedio, estudio usuario, estrategia, diseño comunicacional')))
            ->add('cupos')
            ->add('entrevista',null,array('label' => '¿Entrevista/ Presentación portafolio?','label_attr' => array('data-help' => 'ej. presentación portafolio en entrevista predefinida por contacto')))
            ->add('remuneraciones',null,array('label' => 'Remuneración','label_attr' => array('data-help' => 'monto líquido, si es que hay')))
            ->add('beneficios',null,array('label' => 'Beneficios','label_attr' => array('data-help' => 'locomoción,etc')))
            ->add('descripcion',null,array('label' => 'Breve descripción de proyectos y responsabilidades'));
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
        return 'pdev_practicasbundle_practicatype';
    }
}
