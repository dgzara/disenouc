<?php

namespace pDev\PracticasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use pDev\PracticasBundle\Entity\Evaluacion;
/**
 * EvaluacionSupervisor
 *
 * @ORM\Table(name="nb_practicas_evaluacion_empleador")
 * @ORM\Entity
 */
class EvaluacionSupervisor extends Evaluacion
{
    /**
     * @ORM\OneToOne(targetEntity="pDev\PracticasBundle\Entity\AlumnoPracticante", inversedBy="supervisorEvaluacion")     
     * @ORM\JoinColumn(name="practica_id", referencedColumnName="id", nullable=false)
     */
    private $practica;

    /**
     * @var integer
     *
     * @ORM\Column(name="horas", type="integer",nullable=true)
     */
    private $horas;

    /**
     * @var string
     *
     * @ORM\Column(name="horario", type="string", length=255,nullable=true)
     */
    private $horario;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaDesde", type="datetime",nullable=true)
     */
    private $fechaDesde;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaHasta", type="datetime",nullable=true)
     */
    private $fechaHasta;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Set horas
     *
     * @param integer $horas
     * @return EvaluacionSupervisor
     */
    public function setHoras($horas)
    {
        $this->horas = $horas;
    
        return $this;
    }

    /**
     * Get horas
     *
     * @return integer 
     */
    public function getHoras()
    {
        return $this->horas;
    }

    /**
     * Set horario
     *
     * @param string $horario
     * @return EvaluacionSupervisor
     */
    public function setHorario($horario)
    {
        $this->horario = $horario;
    
        return $this;
    }

    /**
     * Get horario
     *
     * @return string 
     */
    public function getHorario()
    {
        return $this->horario;
    }

    /**
     * Set fechaDesde
     *
     * @param \DateTime $fechaDesde
     * @return EvaluacionSupervisor
     */
    public function setFechaDesde($fechaDesde)
    {
        $this->fechaDesde = $fechaDesde;
    
        return $this;
    }

    /**
     * Get fechaDesde
     *
     * @return \DateTime 
     */
    public function getFechaDesde()
    {
        return $this->fechaDesde;
    }

    /**
     * Set fechaHasta
     *
     * @param \DateTime $fechaHasta
     * @return EvaluacionSupervisor
     */
    public function setFechaHasta($fechaHasta)
    {
        $this->fechaHasta = $fechaHasta;
    
        return $this;
    }

    /**
     * Get fechaHasta
     *
     * @return \DateTime 
     */
    public function getFechaHasta()
    {
        return $this->fechaHasta;
    }

    /**
     * Get supervisor
     *
     * @return \pDev\PracticasBundle\Entity\Supervisor 
     */
    public function getSupervisor()
    {
        return $this->practica->getSupervisor();
    }
    
    /**
     * Set practica
     *
     * @param \pDev\PracticasBundle\Entity\Practica $practica
     * @return EvaluacionSupervisor
     */
    public function setPractica($practica)
    {
        $this->practica = $practica;
    
        return $this;
    }

    /**
     * Get practica
     *
     * @return \pDev\PracticasBundle\Entity\Practica 
     */
    public function getPractica()
    {
        return $this->practica;
    }
    
    /**
     * Get alumno
     *
     * @return \pDev\UserBundle\Entity\Alumno 
     */
    public function getAlumno()
    {
        return $this->practica->getAlumno();
    }
    
    /**
     * Get CalculaNotaFinal
     *
     * @return float 
     */
    public function getCalculaNotaFinal()
    {
        return $this->calculaNota();
    }
}
