<?php

namespace pDev\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Profesor
 *
 * @ORM\Table(name="nb_persona_profesor")
 * @ORM\Entity
 */
class Profesor extends Persona
{
    
    
    /**
     * @ORM\OneToMany(targetEntity="ProfesorAlias", mappedBy="profesor")
     */
    private $aliasSistema;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tipo = "TYPE_ACADEMICO";
        $this->aliasSistema = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    public function __toString() 
    {
        return $this->getNombreCompleto();
    }
    
    /**
     * Add aliasSistema
     *
     * @param \pDev\UserBundle\Entity\ProfesorAlias $aliasSistema
     * @return Profesor
     */
    public function addAliasSistema(\pDev\UserBundle\Entity\ProfesorAlias $aliasSistema)
    {
        $this->aliasSistema[] = $aliasSistema;
    
        return $this;
    }

    /**
     * Remove aliasSistema
     *
     * @param \pDev\UserBundle\Entity\ProfesorAlias $aliasSistema
     */
    public function removeAliasSistema(\pDev\UserBundle\Entity\ProfesorAlias $aliasSistema)
    {
        $this->aliasSistema->removeElement($aliasSistema);
    }

    /**
     * Get aliasSistema
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAliasSistema()
    {
        return $this->aliasSistema;
    }
}
