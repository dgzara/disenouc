<?php

namespace pDev\PracticasBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use pDev\PracticasBundle\Entity\Contacto;

class ContactoToNumberTransformer implements DataTransformerInterface
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

    /**
     * Transforms an object (contacto) to a string (number).
     *
     * @param  Contacto|null $contacto
     * @return string
     */
    public function transform($contacto)
    {
        if (null === $contacto) {
            return "";
        }

        return $contacto->getId();
    }

    /**
     * Transforms a string (number) to an object (contacto).
     *
     * @param  string $number
     *
     * @return Contacto|null
     *
     * @throws TransformationFailedException if object (contacto) is not found.
     */
    public function reverseTransform($number)
    {
        if (!$number) {
            return null;
        }

        $contacto = $this->om
            ->getRepository('pDevPracticasBundle:Contacto')
            ->findOneBy(array('id' => $number))
        ;

        if (null === $contacto) {
            throw new TransformationFailedException(sprintf(
                'An contacto with number "%s" does not exist!',
                $number
            ));
        }

        return $contacto;
    }
}
