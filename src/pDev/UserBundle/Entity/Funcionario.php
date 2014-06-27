<?php

namespace pDev\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use pDev\UserBundle\Entity\Persona;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Funcionario
 *
 * @ORM\Table(name="nb_persona_funcionario")
 * @ORM\Entity
 */
class Funcionario extends Persona
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tipo = "TYPE_FUNCIONARIO";
    }
}
