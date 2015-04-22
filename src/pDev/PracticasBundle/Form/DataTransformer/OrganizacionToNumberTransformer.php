<?php

namespace pDev\PracticasBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use pDev\PracticasBundle\Entity\Organizacion;

class OrganizacionToNumberTransformer implements DataTransformerInterface
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
     * Transforms an object (organizacion) to a string (number).
     *
     * @param  Organizacion|null $organizacion
     * @return string
     */
    public function transform($organizacion)
    {
        if (null === $organizacion) {
            return "";
        }

        return $organizacion->getId();
    }

    /**
     * Transforms a string (number) to an object (organizacion).
     *
     * @param  string $number
     *
     * @return Organizacion|null
     *
     * @throws TransformationFailedException if object (organizacion) is not found.
     */
    public function reverseTransform($number)
    {
        if (!$number) {
            return null;
        }

        $organizacion = $this->om
            ->getRepository('pDevPracticasBundle:Organizacion')
            ->findOneBy(array('id' => $number))
        ;

        if (null === $organizacion) {
            throw new TransformationFailedException(sprintf(
                'An organizacion with number "%s" does not exist!',
                $number
            ));
        }

        return $organizacion;
    }
}
