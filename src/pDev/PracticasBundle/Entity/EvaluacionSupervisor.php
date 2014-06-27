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
     * @ORM\ManyToOne(targetEntity="Supervisor", inversedBy="evaluaciones")
     * @ORM\JoinColumn(name="supervisor_id", referencedColumnName="id", nullable=false)
     */
    private $supervisor;
    
    /**
     * @ORM\OneToMany(targetEntity="pDev\PracticasBundle\Entity\AlumnoPracticante", mappedBy="supervisorEvaluacion")     
     */
    private $practicantes;

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
        $this->practicantes = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set supervisor
     *
     * @param \pDev\PracticasBundle\Entity\Supervisor $supervisor
     * @return EvaluacionSupervisor
     */
    public function setSupervisor(\pDev\PracticasBundle\Entity\Supervisor $supervisor)
    {
        $this->supervisor = $supervisor;
    
        return $this;
    }

    /**
     * Get supervisor
     *
     * @return \pDev\PracticasBundle\Entity\Supervisor 
     */
    public function getSupervisor()
    {
        return $this->supervisor;
    }

    /**
     * Add practicantes
     *
     * @param \pDev\PracticasBundle\Entity\AlumnoPracticante $practicantes
     * @return EvaluacionSupervisor
     */
    public function addPracticante(\pDev\PracticasBundle\Entity\AlumnoPracticante $practicantes)
    {
        $this->practicantes[] = $practicantes;
    
        return $this;
    }

    /**
     * Remove practicantes
     *
     * @param \pDev\PracticasBundle\Entity\AlumnoPracticante $practicantes
     */
    public function removePracticante(\pDev\PracticasBundle\Entity\AlumnoPracticante $practicantes)
    {
        $this->practicantes->removeElement($practicantes);
    }

    /**
     * Get practicantes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPracticantes()
    {
        return $this->practicantes;
    }
}