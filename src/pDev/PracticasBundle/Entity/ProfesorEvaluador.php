<?php

namespace pDev\PracticasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProfesorEvaluador
 *
 * @ORM\Table(name="nb_practicas_profesor")
 * @ORM\Entity
 */
class ProfesorEvaluador
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
     * @ORM\OneToOne(targetEntity="pDev\UserBundle\Entity\Profesor")
     * @ORM\JoinColumn(name="profesor_id", referencedColumnName="id", nullable=false)
     */
    private $profesor;

    /**
     * @ORM\OneToMany(targetEntity="pDev\PracticasBundle\Entity\AlumnoPracticante", mappedBy="profesor")     
     */
    private $practicantes;

    /**
     * @ORM\OneToMany(targetEntity="EvaluacionProfesor", mappedBy="profesor")
     */
    private $evaluaciones;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->practicantes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->evaluaciones = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set profesor
     *
     * @param \pDev\UserBundle\Entity\Profesor $profesor
     * @return ProfesorEvaluador
     */
    public function setProfesor(\pDev\UserBundle\Entity\Profesor $profesor)
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

    /**
     * Add practicantes
     *
     * @param \pDev\PracticasBundle\Entity\AlumnoPracticante $practicantes
     * @return ProfesorEvaluador
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
     * Add evaluaciones
     *
     * @param \pDev\PracticasBundle\Entity\EvaluacionProfesor $evaluaciones
     * @return ProfesorEvaluador
     */
    public function addEvaluacione(\pDev\PracticasBundle\Entity\EvaluacionProfesor $evaluaciones)
    {
        $this->evaluaciones[] = $evaluaciones;
    
        return $this;
    }

    /**
     * Remove evaluaciones
     *
     * @param \pDev\PracticasBundle\Entity\EvaluacionProfesor $evaluaciones
     */
    public function removeEvaluacione(\pDev\PracticasBundle\Entity\EvaluacionProfesor $evaluaciones)
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
}