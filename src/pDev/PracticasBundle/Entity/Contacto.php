<?php

namespace pDev\PracticasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use pDev\UserBundle\Entity\Persona;

/**
 * Contacto
 *
 * @ORM\Table(name="nb_persona_contacto")
 * @ORM\Entity
 */
class Contacto extends Persona
{
    /**
     * @var string
     *
     * @ORM\Column(name="telefono", type="string", length=255)
     */
    private $numeroTelefono;
    
    /**
     * @var string
     *
     * @ORM\Column(name="direccionCalle", type="string", length=255)
     */
    private $direccionCalle;
    
    /**
     * @var string
     *
     * @ORM\Column(name="area", type="string", length=255, nullable=true)
     */
    private $area;
    
    /**
     * @ORM\ManyToMany(targetEntity="Organizacion", inversedBy="contactos")
     * @ORM\JoinTable(name="nb_persona_contactos_organizaciones")
     */
    private $organizaciones;
    
    /**
     * @ORM\OneToMany(targetEntity="pDev\PracticasBundle\Entity\Practica", mappedBy="contacto")     
     */
    private $practicas;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tipo = "TYPE_PRACTICAS_CONTACTO";
        $this->organizaciones = new \Doctrine\Common\Collections\ArrayCollection();
        $this->practicas = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    public function __toString()
    {
        return "".$this->getNombres()." ".$this->getApellidoPaterno();
    }
    
    /**
     * Set numeroTelefono
     *
     * @param string $numeroTelefono
     * @return Contacto
     */
    public function setNumeroTelefono($numeroTelefono)
    {
        $this->numeroTelefono = $numeroTelefono;
    
        return $this;
    }

    /**
     * Get numeroTelefono
     *
     * @return string 
     */
    public function getNumeroTelefono()
    {
        return $this->numeroTelefono;
    }
    
    /**
     * Set direccionCalle
     *
     * @param string $direccionCalle
     * @return Contacto
     */
    public function setDireccionCalle($direccionCalle)
    {
        $this->direccionCalle = $direccionCalle;
    
        return $this;
    }

    /**
     * Get direccionCalle
     *
     * @return string 
     */
    public function getDireccionCalle()
    {
        return $this->direccionCalle;
    }
    
    /**
     * Set area
     *
     * @param string $area
     * @return Contacto
     */
    public function setArea($area)
    {
        $this->area = $area;
    
        return $this;
    }

    /**
     * Get area
     *
     * @return string 
     */
    public function getArea()
    {
        return $this->area;
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
    
    /**
     * Add organizacion
     *
     * @param \pDev\PracticasBundle\Entity\Organizacion $organizacion
     * @return Supervisor
     */
    public function addOrganizacion(\pDev\PracticasBundle\Entity\Organizacion $organizacion)
    {
        $this->organizaciones[] = $organizacion;
    
        return $this;
    }

    /**
     * Remove organizaciones
     *
     * @param \pDev\PracticasBundle\Entity\Organizacion $organizacion
     */
    public function removeOrganizacion(\pDev\PracticasBundle\Entity\Organizacion $organizacion)
    {
        $this->organizaciones->removeElement($organizacion);
    }

    /**
     * Get organizaciones
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOrganizaciones()
    {
        return $this->organizaciones;
    }
}
