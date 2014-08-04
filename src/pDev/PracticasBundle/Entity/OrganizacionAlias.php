<?php

namespace pDev\PracticasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrganizacionAlias
 *
 * @ORM\Table(name="nb_practicas_organizacion_alias")
 * @ORM\Entity
 */
class OrganizacionAlias
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
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255)
     */
    private $nombre;

    /**
     * @ORM\ManyToOne(targetEntity="Organizacion", inversedBy="aliases")
     * @ORM\JoinColumn(name="organizacion_id", referencedColumnName="id", nullable=false)
     */
    private $organizacion;
    
    /**
     * @ORM\OneToMany(targetEntity="pDev\PracticasBundle\Entity\Practica", mappedBy="organizacionAlias")     
     */
    private $practicas;
    
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
     * Constructor
     */
    public function __construct()
    {
        $this->tipo = "TYPE_PRACTICAS_CONTACTO";
        $this->practicas = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Get nombre
     *
     * @return string 
     */
    public function __toString()
    {
        return "".$this->nombre;
    }
    
    /**
     * Set nombre
     *
     * @param string $nombre
     * @return OrganizacionAlias
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    
        return $this;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set organizacion
     *
     * @param \pDev\PracticasBundle\Entity\Organizacion $organizacion
     * @return OrganizacionAlias
     */
    public function setOrganizacion(\pDev\PracticasBundle\Entity\Organizacion $organizacion)
    {
        $this->organizacion = $organizacion;
    
        return $this;
    }

    /**
     * Get organizacion
     *
     * @return \pDev\PracticasBundle\Entity\Organizacion 
     */
    public function getOrganizacion()
    {
        return $this->organizacion;
    }
    
    /**
     * Add practicas
     *
     * @param \pDev\PracticasBundle\Entity\Practica $practicas
     * @return Contacto
     */
    public function addPractica(\pDev\PracticasBundle\Entity\Practica $practicas)
    {
        $this->practicas[] = $practicas;
    
        return $this;
    }

    /**
     * Remove practicas
     *
     * @param \pDev\PracticasBundle\Entity\Practica $practicas
     */
    public function removePractica(\pDev\PracticasBundle\Entity\Practica $practicas)
    {
        $this->practicas->removeElement($practicas);
    }

    /**
     * Get practicas
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPracticas()
    {
        return $this->practicas;
    }
}
