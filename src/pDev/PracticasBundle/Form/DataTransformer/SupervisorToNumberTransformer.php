<?php

namespace pDev\PracticasBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use pDev\PracticasBundle\Entity\Supervisor;

class SupervisorToNumberTransformer implements DataTransformerInterface
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
     * Transforms an object (supervisor) to a string (number).
     *
     * @param  Supervisor|null $supervisor
     * @return string
     */
    public function transform($supervisor)
    {
        if (null === $supervisor) {
            return "";
        }

        return $supervisor->getId();
    }

    /**
     * Transforms a string (number) to an object (supervisor).
     *
     * @param  string $number
     *
     * @return Supervisor|null
     *
     * @throws TransformationFailedException if object (supervisor) is not found.
     */
    public function reverseTransform($number)
    {
        if (!$number) {
            return null;
        }

        $supervisor = $this->om
            ->getRepository('pDevPracticasBundle:Supervisor')
            ->findOneBy(array('id' => $number))
        ;

        if (null === $supervisor) {
            throw new TransformationFailedException(sprintf(
                'An supervisor with number "%s" does not exist!',
                $number
            ));
        }

        return $supervisor;
    }
}
