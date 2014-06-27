<?php

namespace pDev\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use pDev\UserBundle\Entity\Persona;

/**
 * Alumno
 *
 * @ORM\Table(name="nb_persona_alumno")
 * @ORM\Entity
 */
class Alumno extends Persona
{
    /**
     * @var string
     *
     * @ORM\Column(name="numeroalumno", type="string")
     */
    private $numeroAlumno;
    
    /**
     * @var string
     *
     * @ORM\Column(name="sexo", type="string",nullable=true)
     */
    private $sexo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="nacimientoFecha", type="date",nullable=true)
     */
    private $nacimientoFecha;

    /**
     * @var string
     *
     * @ORM\Column(name="pais", type="string",nullable=true)
     */
    private $pais;

    /**
     * @var string
     *
     * @ORM\Column(name="estadoCivil", type="string",nullable=true)
     */
    private $estadoCivil;   
    
    /**
     * @ORM\ManyToMany(targetEntity="Periodo", mappedBy="alumnos")
     */
    private $periodos;
    
    /**
     * Set numeroAlumno
     *
     * @param string $numeroAlumno
     * @return Alumno
     */
    public function setNumeroAlumno($numeroAlumno)
    {
        $this->numeroAlumno = $numeroAlumno;
    
        return $this;
    }

    /**
     * Get numeroAlumno
     *
     * @return string 
     */
    public function getNumeroAlumno()
    {
        return $this->numeroAlumno;
    }
    
    /**
     * Set sexo
     *
     * @param string $sexo
     * @return Alumno
     */
    public function setSexo($sexo)
    {
        $this->sexo = $sexo;
    
        return $this;
    }

    /**
     * Get sexo
     *
     * @return string 
     */
    public function getSexo()
    {
        return $this->sexo;
    }

    /**
     * Set nacimientoFecha
     *
     * @param \DateTime $nacimientoFecha
     * @return Alumno
     */
    public function setNacimientoFecha($nacimientoFecha)
    {
        $this->nacimientoFecha = $nacimientoFecha;
    
        return $this;
    }

    /**
     * Get nacimientoFecha
     *
     * @return \DateTime 
     */
    public function getNacimientoFecha()
    {
        return $this->nacimientoFecha;
    }

    /**
     * Set pais
     *
     * @param string $pais
     * @return Alumno
     */
    public function setPais($pais)
    {
        $this->pais = $pais;
    
        return $this;
    }

    /**
     * Get pais
     *
     * @return string 
     */
    public function getPais()
    {
        return $this->pais;
    }

    /**
     * Set estadoCivil
     *
     * @param string $estadoCivil
     * @return Alumno
     */
    public function setEstadoCivil($estadoCivil)
    {
        $this->estadoCivil = $estadoCivil;
    
        return $this;
    }

    /**
     * Get estadoCivil
     *
     * @return string 
     */
    public function getEstadoCivil()
    {
        return $this->estadoCivil;
    }

    

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tipo = "TYPE_ALUMNO";
        $this->periodos = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    
    /**
     * Add periodos
     *
     * @param \pDev\UserBundle\Entity\Periodo $periodos
     * @return Curso
     */
    public function addPeriodo(\pDev\UserBundle\Entity\Periodo $periodos)
    {
        $this->periodos[] = $periodos;
    
        return $this;
    }

    /**
     * Remove periodos
     *
     * @param \pDev\UserBundle\Entity\Periodo $periodos
     */
    public function removePeriodo(\pDev\UserBundle\Entity\Periodo $periodos)
    {
        $this->periodos->removeElement($periodos);
    }
    
    /**
     * Get periodos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPeriodos()
    {
        return $this->periodos;
    }
    
}
