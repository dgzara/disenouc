<?php

namespace pDev\PracticasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Proyecto
 *
 * @ORM\Table(name="nb_practicas_practicante_proyecto")
 * @ORM\Entity
 */
class Proyecto
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
     * @ORM\Column(name="nombre", type="string", length=255)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="text")
     */
    private $descripcion;

    /**
     * @ORM\ManyToOne(targetEntity="pDev\PracticasBundle\Entity\AlumnoPracticante", inversedBy="proyectos")
     * @ORM\JoinColumn(name="practica_id", referencedColumnName="id", nullable=false)
     */
    private $practicante;

    /**
     * @ORM\OneToMany(targetEntity="pDev\PracticasBundle\Entity\ProyectoTask", mappedBy="proyecto")
     */
    private $tareas;


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
     * Set nombre
     *
     * @param string $nombre
     * @return CriterioTipo
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    
        return $this;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre()
    {
        return $this->nombre;
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
     * Constructor
     */
    public function __construct()
    {
        $this->tareas = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Set practicante
     *
     * @param \pDev\PracticasBundle\Entity\AlumnoPracticante $practicante
     * @return Proyecto
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

    /**
     * Add tareas
     *
     * @param \pDev\PracticasBundle\Entity\ProyectoTask $tareas
     * @return Proyecto
     */
    public function addTarea(\pDev\PracticasBundle\Entity\ProyectoTask $tareas)
    {
        $this->tareas[] = $tareas;
    
        return $this;
    }

    /**
     * Remove tareas
     *
     * @param \pDev\PracticasBundle\Entity\ProyectoTask $tareas
     */
    public function removeTarea(\pDev\PracticasBundle\Entity\ProyectoTask $tareas)
    {
        $this->tareas->removeElement($tareas);
    }

    /**
     * Get tareas
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTareas()
    {
        return $this->tareas;
    }
}