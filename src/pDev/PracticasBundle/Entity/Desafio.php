<?php

namespace pDev\PracticasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Proyecto
 *
 * @ORM\Table(name="nb_practicas_practicante_desafio")
 * @ORM\Entity
 */
class Desafio
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
     * @ORM\Column(name="descripcion", type="text")
     */
    private $descripcion;

    /**
     * @ORM\ManyToOne(targetEntity="pDev\PracticasBundle\Entity\AlumnoPracticante", inversedBy="desafios")
     * @ORM\JoinColumn(name="practica_id", referencedColumnName="id", nullable=false)
     */
    private $practicante;

    
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
     * Set descripcion
     *
     * @param string $descripcion
     * @return CriterioTipo
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    
        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string 
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    

    /**
     * Set practicante
     *
     * @param \pDev\PracticasBundle\Entity\AlumnoPracticante $practicante
     * @return Desafio
     */
    public function setPracticante(\pDev\PracticasBundle\Entity\AlumnoPracticante $practicante)
    {
        $this->practicante = $practicante;
    
        return $this;
    }

    /**
     * Get practicante
     *
     * @return \pDev\PracticasBundle\Entity\AlumnoPracticante 
     */
    public function getPracticante()
    {
        return $this->practicante;
    }
}