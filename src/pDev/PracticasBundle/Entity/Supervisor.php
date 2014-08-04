<?php

namespace pDev\PracticasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use pDev\UserBundle\Entity\Persona;

/**
 * Supervisor
 *
 * @ORM\Table(name="nb_practicas_supervisor")
 * @ORM\Entity
 */
class Supervisor extends Persona
{
    /**
     * @ORM\OneToMany(targetEntity="EvaluacionSupervisor", mappedBy="supervisor")
     */
    private $evaluaciones;

    /**
     * @ORM\OneToMany(targetEntity="pDev\PracticasBundle\Entity\AlumnoPracticante", mappedBy="supervisor")     
     */
    private $practicantes;
    
    /**
     * @var string
     *
     * @ORM\Column(name="cargo", type="string", length=255)
     */
    private $cargo;
    
    public function __toString()
    {
        return "".$this->getNombres()." ".$this->getApellidoPaterno();
    }
    
    /**
     * Set cargo
     *
     * @param string $cargo
     * @return Supervisor
     */
    public function setCargo($cargo)
    {
        $this->cargo = $cargo;
    
        return $this;
    }

    /**
     * Get cargo
     *
     * @return string 
     */
    public function getCargo()
    {
        return $this->cargo;
    }

    /**
     * Get practicas
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPracticas()
    {
        $practicas = array();
        
        foreach($this->practicantes as $practicante)
            $practicas = array_merge($practicas,$practicante->getPractica());
        
        return $practicas;
        
    }

    /**
     * Add evaluaciones
     *
     * @param \pDev\PracticasBundle\Entity\EvaluacionSupervisor $evaluaciones
     * @return Supervisor
     */
    public function addEvaluacione(\pDev\PracticasBundle\Entity\EvaluacionSupervisor $evaluaciones)
    {
        $this->evaluaciones[] = $evaluaciones;
    
        return $this;
    }

    /**
     * Remove evaluaciones
     *
     * @param \pDev\PracticasBundle\Entity\EvaluacionSupervisor $evaluaciones
     */
    public function removeEvaluacione(\pDev\PracticasBundle\Entity\EvaluacionSupervisor $evaluaciones)
    {
        $this->evaluaciones->removeElement($evaluaciones);
    }

    /**
     * Get evaluaciones
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEvaluaciones()
    {
        return $this->evaluaciones;
    }

    /**
     * Add practicantes
     *
     * @param \pDev\PracticasBundle\Entity\AlumnoPracticante $practicantes
     * @return Supervisor
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
     * Constructor
     */
    public function __construct()
    {
        $this->practicas = new \Doctrine\Common\Collections\ArrayCollection();
        $this->evaluaciones = new \Doctrine\Common\Collections\ArrayCollection();
        $this->practicantes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tipo = "TYPE_PRACTICAS_SUPERVISOR";
    }
}
