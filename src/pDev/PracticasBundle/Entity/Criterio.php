<?php

namespace pDev\PracticasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Criterio
 *
 * @ORM\Table(name="nb_practicas_criterio")
 * @ORM\Entity
 */
class Criterio
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
     * @ORM\ManyToOne(targetEntity="CriterioTipo")
     * @ORM\JoinColumn(name="tipo_id", referencedColumnName="id",nullable=false)
     */
    private $criterioTipo;
    
    /**
     * @ORM\ManyToOne(targetEntity="Evaluacion", inversedBy="criterios", cascade={"persist"})
     * @ORM\JoinColumn(name="evaluacion_id", referencedColumnName="id", nullable=false)
     */
    private $evaluacion;

    /**
     * @var string
     *
     * @ORM\Column(name="valor", type="string", length=255, nullable=true)
     */
    private $valor;

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
     * Set valor
     *
     * @param string $valor
     * @return Criterio
     */
    public function setValor($valor)
    {
        $this->valor = $valor;
    
        return $this;
    }

    /**
     * Get valor
     *
     * @return string 
     */
    public function getValor()
    {
        return $this->valor;
    }

    /**
     * Set criterioTipo
     *
     * @param \pDev\PracticasBundle\Entity\CriterioTipo $criterioTipo
     * @return Criterio
     */
    public function setCriterioTipo(\pDev\PracticasBundle\Entity\CriterioTipo $criterioTipo)
    {
        $this->criterioTipo = $criterioTipo;
    
        return $this;
    }

    /**
     * Get criterioTipo
     *
     * @return \pDev\PracticasBundle\Entity\CriterioTipo 
     */
    public function getCriterioTipo()
    {
        return $this->criterioTipo;
    }

    /**
     * Set evaluacion
     *
     * @param \pDev\PracticasBundle\Entity\Evaluacion $evaluacion
     * @return Criterio
     */
    public function setEvaluacion(\pDev\PracticasBundle\Entity\Evaluacion $evaluacion)
    {
        $this->evaluacion = $evaluacion;
    
        return $this;
    }

    /**
     * Get evaluacion
     *
     * @return \pDev\PracticasBundle\Entity\Evaluacion 
     */
    public function getEvaluacion()
    {
        return $this->evaluacion;
    }
}
