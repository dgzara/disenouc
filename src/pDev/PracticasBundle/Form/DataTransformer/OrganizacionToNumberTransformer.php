<?php

namespace pDev\PracticasBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use pDev\PracticasBundle\Entity\OrganizacionAlias;

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
     * Transforms an object (organizacionAlias) to a string (number).
     *
     * @param  OrganizacionAlias|null $organizacionAlias
     * @return string
     */
    public function transform($organizacionAlias)
    {
        if (null === $organizacionAlias) {
            return "";
        }

        return $organizacionAlias->getId();
    }

    /**
     * Transforms a string (number) to an object (organizacionAlias).
     *
     * @param  string $number
     *
     * @return OrganizacionAlias|null
     *
     * @throws TransformationFailedException if object (organizacionAlias) is not found.
     */
    public function reverseTransform($number)
    {
        if (!$number) {
            return null;
        }

        $organizacionAlias = $this->om
            ->getRepository('pDevPracticasBundle:OrganizacionAlias')
            ->findOneBy(array('id' => $number))
        ;

        if (null === $organizacionAlias) {
            throw new TransformationFailedException(sprintf(
                'An organizacionAlias with number "%s" does not exist!',
                $number
            ));
        }

        return $organizacionAlias;
    }
}
