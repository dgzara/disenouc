<?php

namespace pDev\PracticasBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use pDev\PracticasBundle\Form\DataTransformer\ContactoToNumberTransformer;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ContactoSelectorType extends AbstractType
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new ContactoToNumberTransformer($this->om);
        $builder->addModelTransformer($transformer);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'invalid_message' => 'The selected contacto does not exist',
        ));
    }

    public function getParent()
    {
        return 'hidden';
    }

    public function getName()
    {
        return 'contacto_selector';
    }
}

