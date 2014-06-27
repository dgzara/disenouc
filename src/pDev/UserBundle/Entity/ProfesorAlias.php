<?php

namespace pDev\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * ProfesorAlias
 *
 * @ORM\Table(name="nb_persona_profesor_alias")
 * @ORM\Entity
 */
class ProfesorAlias
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
	 * @ORM\ManyToOne(targetEntity="Profesor", inversedBy="aliasSistema")
     * @ORM\JoinColumn(name="profesor_id", referencedColumnName="id", nullable=true)
	 */
    private $profesor;
    
    /**
     * @var string
     *
     * @ORM\Column(name="alias", type="string")
     */
    private $alias;
    

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set alias
     *
     * @param string $alias
     * @return ProfesorAlias
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
    
        return $this;
    }

    /**
     * Get alias
     *
     * @return string 
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Set profesor
     *
     * @param \pDev\UserBundle\Entity\Profesor $profesor
     * @return ProfesorAlias
     */
    public function setProfesor(\pDev\UserBundle\Entity\Profesor $profesor = null)
    {
        $this->profesor = $profesor;
    
        return $this;
    }

    /**
     * Get profesor
     *
     * @return \pDev\UserBundle\Entity\Profesor 
     */
    public function getProfesor()
    {
        return $this->profesor;
    }
}