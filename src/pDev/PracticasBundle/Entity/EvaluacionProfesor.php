<?php

namespace pDev\PracticasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EvaluacionEmpleador
 *
 * @ORM\Table(name="nb_practicas_evaluacion_profesor")
 * @ORM\Entity
 */
class EvaluacionProfesor extends Evaluacion
{
    /**
     * @ORM\OneToOne(targetEntity="pDev\PracticasBundle\Entity\AlumnoPracticante", inversedBy="profesorEvaluacion")     
     * @ORM\JoinColumn(name="practica_id", referencedColumnName="id", nullable=false)
     */
    private $practica;

    /**
     * @var integer
     *
     * @ORM\Column(name="year", type="integer", nullable=true)     
     */
    private $year;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="semestre", type="integer", nullable=true)     
     */
    private $semestre;
    
    /**
     * @var string
     *
     * @ORM\Column(name="tipo", type="string", length=255)
     */
    private $tipo;
    
    /**
     * @var float
     *
     * @ORM\Column(name="descuento_nota", type="float", nullable=true)
     */
    private $descuento;
    
    /**
     * @var float
     *
     * @ORM\Column(name="nota_final", type="float", nullable=true)
     */
    private $notaFinal;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->descuento = 0;
    }
    
    /**
     * Set year
     *
     * @param integer $year
     * @return EvaluacionProfesor
     */
    public function setYear($year)
    {
        $this->year = $year;
    
        return $this;
    }

    /**
     * Get year
     *
     * @return integer 
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set semestre
     *
     * @param integer $semestre
     * @return EvaluacionProfesor
     */
    public function setSemestre($semestre)
    {
        $this->semestre = $semestre;
    
        return $this;
    }

    /**
     * Get semestre
     *
     * @return integer 
     */
    public function getSemestre()
    {
        return $this->semestre;
    }

    /**
     * Get profesor
     *
     * @return \pDev\PracticasBundle\Entity\ProfesorEvaluador 
     */
    public function getProfesor()
    {
        return $this->practica->getProfesor();
    }

    /**
     * Set tipo
     *
     * @param string $tipo
     * @return Practica
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    
        return $this;
    }

    /**
     * Get tipo
     *
     * @return string 
     */
    public function getTipo()
    {
        return $this->tipo;
    }
    
    /**
     * Set descuento
     *
     * @param float $descuento
     * @return EvaluacionProfesor
     */
    public function setDescuento($descuento)
    {
        $this->descuento = $descuento;
    
        return $this;
    }

    /**
     * Get descuento
     *
     * @return float 
     */
    public function getDescuento()
    {
        return $this->descuento;
    }
    
    /**
     * Set practica
     *
     * @param \pDev\PracticasBundle\Entity\Practica $practica
     * @return EvaluacionProfesor
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
     * Get CalculaNotaFinal
     *
     * @return float 
     */
    public function getCalculaNotaFinal()
    {
        return $this->calculaNota() - $this->getDescuento();
    }
    
    /**
     * Set nota
     *
     * @param float $nota
     * @return EvaluacionProfesor
     */
    public function setNotaFinal($nota)
    {
        $this->notaFinal = $nota;
    
        return $this;
    }

    /**
     * Get nota
     *
     * @return float 
     */
    public function getNotaFinal()
    {
        return $this->notaFinal;
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
}
