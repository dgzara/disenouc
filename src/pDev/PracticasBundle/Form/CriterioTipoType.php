<?php

namespace pDev\PracticasBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CriterioTipoType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('aspecto','choice',array('choices'   => array('Contenido' => 'Contenido', 'Formal' => 'Formal')))
            ->add('tipoPractica','choice',array('choices'   => array('Oficina' => 'Oficina', 'Servicio' => 'Servicio')))
            ->add('tipoEvaluador','choice',array('choices'   => array('Profesor' => 'Profesor', 'Supervisor' => 'Supervisor')))
            ->add('nombre')
            ->add('descripcion')
            ->add('explicacion');
            
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'pDev\PracticasBundle\Entity\CriterioTipo'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'pdev_practicasbundle_criteriotipotype';
    }
}
