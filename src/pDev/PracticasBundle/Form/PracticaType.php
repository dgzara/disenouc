<?php

namespace pDev\PracticasBundle\Form;

use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class PracticaType extends AbstractType
{
    private $securityContext;

    public function __construct(SecurityContext $securityContext)
    {
        $this->securityContext = $securityContext;
    }
    
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $this->securityContext->getToken()->getUser();
        $builder
            ->add('nombre', null, array(
                'label' => 'Nombre de la práctica'
            ))
            ->add('organizacion', 'organizacion_selector', array(
                'label' => 'Organización',
                'required' => true
            ))
            ->add('tipo', 'choice', array(
                'choices'   => array('Oficina' => 'Oficina', 'Servicio' => 'Servicio'),
                'required'  => false,
                'label_attr' => array(  
                    'data-help' => '- Servicio: está orientado a situar al estudiante en la realidad social, enfrentándolo a problemas complejos, donde desde el diseño aporte, con una postura ética, al impacto positivo en el desarrollo sustentable, el beneficio social y la mejora de la calidad de vida de las personas. 

- Oficina: está orientada a que el estudiante observe y comprenda desde la experiencia laboral, el valor del diseño en un mercado influenciado por variables de orden social, productivo, económico, ambiental cultural y político')
            ))
            ->add('contacto')
            ->add('descripcion',null,array('label' => 'Breve descripción de proyectos y responsabilidades'))
            ->add('jornadas', 'choice', array(
                'choices'   => array(
                    'Part-time' => 'Part-time', 
                    'Full-time' => 'Full-time'),
                'required'  => true,
                'label'=> 'Tipo de jornada',
                'label_attr' => array('data-help' => '240 horas')
            ))
            ->add('fechaInicio', 'date', array(
                'widget' => 'single_text',                                            
                'invalid_message'=>'Valor no válido',
                'label' => 'Fecha de inicio',
                'format' => 'dd-MM-yyyy',
                'attr' => array('placeholder' => 'dd-mm-aaaa')
            ))
            ->add('duracionCantidad', null, array(
                'label' => 'Duración',
                'attr' => array('min' => 0)
            ))
            ->add('duracionUnidad', 'choice', array(
                'choices'   => array(
                    'días' => 'días', 
                    'semanas' => 'semanas',
                    'meses' => 'meses'),
            ))
            ->add('manejoSoftware',null,array('label' => 'Manejo de software','label_attr' => array('data-help' => 'ej.Adobe Photoshop, Topsolid, Rhino, Illustrator, etc')))
            ->add('interes',null,array('label' => 'Interés','label_attr' => array('data-help' => 'ej.Industrial, gráfico, ambos, multimedio, estudio usuario, estrategia, diseño comunicacional')))
            ->add('cupos', null, array(
                'attr' => array('min' => 0)
            ))
            ->add('entrevista',null,array(
                'label' => '¿Requiere entrevista o presentación de un portafolio?',
                'label_attr' => array(
                    'data-help' => 'ej. presentación portafolio en entrevista predefinida por contacto'
                )
            ))
            ->add('remuneraciones',null,array('label' => 'Remuneración','label_attr' => array('data-help' => 'monto líquido, si es que hay')))
            ->add('beneficios',null,array('label' => 'Beneficios','label_attr' => array('data-help' => 'locomoción,etc')))
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
        return 'pdev_practicasbundle_practicatype';
    }
}
