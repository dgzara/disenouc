<?php

namespace pDev\PracticasBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class EvaluacionProfesorAsignarType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('profesorOriginal', 'entity', array(
                                'class' => 'pDevUserBundle:Profesor',
                                'query_builder' => function(EntityRepository $er) {

                                    return $er->createQueryBuilder('u')                                                
                                                ->orderBy('u.nombres', 'ASC');
                                },
            'mapped'=>false,
                                        'label'=>'Profesor'))
            ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'pDev\PracticasBundle\Entity\EvaluacionProfesor'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'pdev_practicasbundle_evaluacionprofesorasignartype';
    }
}
