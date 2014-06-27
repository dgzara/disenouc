<?php

namespace pDev\PracticasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Evaluacion
 *
 * @ORM\Table(name="nb_practicas_evaluacion")
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discriminator", type="string")
 * @ORM\DiscriminatorMap({
 *      "empleador" = "pDev\PracticasBundle\Entity\EvaluacionSupervisor",
 *      "profesor" = "pDev\PracticasBundle\Entity\EvaluacionProfesor",
 * })
 */
class Evaluacion
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
     * @ORM\Column(name="observaciones", type="text",nullable =true)
     */
    private $observaciones;

    /**
     * @ORM\OneToMany(targetEntity="Criterio", mappedBy="evaluacion")
     */
    protected $criterios;

    /**
     * @var float
     *
     * @ORM\Column(name="nota", type="float")
     */
    protected $nota;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->criterios = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
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
     * Set observaciones
     *
     * @param string $observaciones
     * @return Evaluacion
     */
    public function setObservaciones($observaciones)
    {
        $this->observaciones = $observaciones;
    
        return $this;
    }

    /**
     * Get observaciones
     *
     * @return string 
     */
    public function getObservaciones()
    {
        return $this->observaciones;
    }

    /**
     * Add criterios
     *
     * @param \pDev\PracticasBundle\Entity\criterio $criterios
     * @return Evaluacion
     */
    public function addCriterio(\pDev\PracticasBundle\Entity\criterio $criterios)
    {
        $this->criterios[] = $criterios;
    
        return $this;
    }

    /**
     * Remove criterios
     *
     * @param \pDev\PracticasBundle\Entity\criterio $criterios
     */
    public function removeCriterio(\pDev\PracticasBundle\Entity\criterio $criterios)
    {
        $this->criterios->removeElement($criterios);
    }

    /**
     * Get criterios
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCriterios()
    {
        return $this->criterios;
    }
    
    /**
     * Get nota
     *
     * @return float 
     */
    public function calculaNota()
    {
        $return = 0.0;
        
        foreach ($this->criterios as $criterio)
        {
            $valor = $criterio->getValor()?$criterio->getValor():2;
            $return += floatval($valor);
        }
        
        $total = count($this->criterios);
        if($total>0)
            $return = $return/$total;
        else
            $return = 0;
        
        return round($return, 1);
    }
    
    /**
     * Set nota
     *
     * @param float $nota
     * @return EvaluacionProfesor
     */
    public function setNota($nota)
    {
        $this->nota = $nota;
    
        return $this;
    }

    /**
     * Get nota
     *
     * @return float 
     */
    public function getNota()
    {
        return $this->nota;
    }
}