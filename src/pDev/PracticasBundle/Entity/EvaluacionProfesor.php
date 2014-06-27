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
     * @ORM\ManyToOne(targetEntity="ProfesorEvaluador", inversedBy="evaluaciones")
     * @ORM\JoinColumn(name="evaluador_id", referencedColumnName="id", nullable=false)
     */
    private $profesor;
    
    /**
     * @ORM\OneToMany(targetEntity="pDev\PracticasBundle\Entity\AlumnoPracticante", mappedBy="profesorEvaluacion")     
     */
    private $practicantes;

    /**
     * @var string
     *
     * @ORM\Column(name="tituloInforme", type="string", length=255,nullable =true)
     */
    private $tituloInforme;
    
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
     * @ORM\Column(name="descuento_nota", type="float")
     */
    private $descuento;
    
    /**
     * @var float
     *
     * @ORM\Column(name="nota_final", type="float")
     */
    private $notaFinal;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->practicantes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->descuento = 0;
    }
    
    /**
     * Set tituloInforme
     *
     * @param string $tituloInforme
     * @return EvaluacionProfesor
     */
    public function setTituloInforme($tituloInforme)
    {
        $this->tituloInforme = $tituloInforme;
    
        return $this;
    }

    /**
     * Get tituloInforme
     *
     * @return string 
     */
    public function getTituloInforme()
    {
        return $this->tituloInforme;
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
     * Set profesor
     *
     * @param \pDev\PracticasBundle\Entity\ProfesorEvaluador $profesor
     * @return EvaluacionProfesor
     */
    public function setProfesor(\pDev\PracticasBundle\Entity\ProfesorEvaluador $profesor)
    {
        $this->profesor = $profesor;
    
        return $this;
    }

    /**
     * Get profesor
     *
     * @return \pDev\PracticasBundle\Entity\ProfesorEvaluador 
     */
    public function getProfesor()
    {
        return $this->profesor;
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
     * Add practicantes
     *
     * @param \pDev\PracticasBundle\Entity\AlumnoPracticante $practicantes
     * @return EvaluacionProfesor
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
     * Get descuento
     *
     * @return float 
     */
    public function calculaNotaFinal()
    {
        return $this->getNota() - $this->getDescuento();
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
}