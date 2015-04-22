<?php

namespace pDev\PracticasBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class OrganizacionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre', null, array('label' => 'Nombre'))
            ->add('rut',null,array('label'=>'RUT de la organización'))
            ->add('rubro')
            ->add('descripcion',null,array('label'=>'Descripción general'))
            ->add('pais',null,array('label'=>'País'))
            ->add('web',null,array('label'=>'Dirección web'))            
            ->add('personasTotal',null,array('label'=>'N° personas','label_attr' => array('data-help' => 'Número de personas que trabajan en la organización. Debe ser mayor a 5 personas')))
            ->add('antiguedad',null,array('label'=>'Años de antiguedad'))
            ->add('profilePic',null,array('label'=>'Logotipo', 'required'=>false))
            ->add('isFileChanged','hidden')
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'pDev\PracticasBundle\Entity\Organizacion'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'pdev_practicasbundle_organizaciontype';
    }
}
