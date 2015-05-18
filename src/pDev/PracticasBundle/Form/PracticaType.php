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
    private $organizacion;

    public function __construct(SecurityContext $securityContext, $organizacion = null)
    {
        $this->securityContext = $securityContext;
        $this->organizacion = $organizacion;        
    }
    
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $this->securityContext->getToken()->getUser();
        $organizacion = $this->organizacion;
        
        $builder
            ->add('nombre', null, array(
                'label' => 'Nombre de la oferta de práctica que solicita',
                'attr' => array('placeholder' => 'Ingrese un nombre')
            ))
            ->add('organizacion', 'organizacion_selector', array(
                'label' => 'Organización',
                'required' => true,
                'attr' => array('placeholder' => 'Ingrese el nombre de la organización')
            ))
            ->add('tipo', 'choice', array(
                'choices'   => array('Oficina' => 'Oficina', 'Servicio' => 'Servicio'),
                'required'  => false,
                'label' => 'Práctica de oficina o servicio (establecido por Coordinación de Practicas)',
                'label_attr' => array(  
                    'data-help' => '- Servicio: está orientado a situar al estudiante en la realidad social, enfrentándolo a problemas complejos, donde desde el diseño aporte, con una postura ética, al impacto positivo en el desarrollo sustentable, el beneficio social y la mejora de la calidad de vida de las personas. 

- Oficina: está orientada a que el estudiante observe y comprenda desde la experiencia laboral, el valor del diseño en un mercado influenciado por variables de orden social, productivo, económico, ambiental cultural y político')
            ));
            
        if($organizacion){
            if($organizacion->getContactos()->count() > 0)
            {
                $builder->add('contacto', 'entity' ,array(
                    'label' => 'Contacto',
                    'required' => true,
                    'class'=> 'pDevPracticasBundle:Contacto',
                    'query_builder' => function ($repository) use ($organizacion)
                        {                        
                            return $repository->createQueryBuilder('c')
                                ->leftjoin('c.organizaciones', 'o')
                                ->where('o.id = :id')
                                ->setParameter('id', $organizacion->getId());
                        },
                ));
            }
            else{
                $builder->add('contacto', new ContactoOrganizacionType(), array());
            }
        }
            
        $builder
            ->add('supervisor', new SupervisorOrganizacionType())
            ->add('descripcion', null, array('label' => 'Breve descripción de proyectos y responsabilidades'))
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
            ->add('manejoSoftware', null, array('label' => 'Manejo de software','attr' => array('placeholder' => 'ej. Adobe Photoshop, Topsolid, Rhino, Illustrator, etc')))
            ->add('interes', null, array('label' => 'Interés','attr' => array('placeholder' => 'ej. Industrial, gráfico, ambos, multimedio, estudio usuario, estrategia, diseño comunicacional')))
            ->add('cupos', null, array(
                'attr' => array('min' => 0)
            ))
            ->add('entrevista', null, array(
                'label' => '¿Requiere entrevista o presentación de un portafolio?',
                'attr' => array('placeholder' => 'ej. presentación portafolio en entrevista predefinida por contacto')
            ))
            ->add('remuneraciones', null, array('label' => 'Remuneración','attr' => array('placeholder' => 'Monto líquido, si es que hay')))
            ->add('beneficios', null, array('label' => 'Beneficios','attr' => array('placeholder' => 'Locomoción, etc')))
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
