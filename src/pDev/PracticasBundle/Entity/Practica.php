<?php

namespace pDev\PracticasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use pDev\UserBundle\Entity\User;

/**
 * Practica
 *
 * @ORM\Table(name="nb_practicas_practica")
 * @ORM\Entity
 */
class Practica
{
    const ESTADO_PENDIENTE = "estado.pendiente";
    const ESTADO_APROBADA = "estado.aprobada";
    const ESTADO_RECHAZADA = "estado.rechazada";
    const ESTADO_PUBLICADA = "estado.publicada";
    
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
     * @ORM\Column(name="tipo", type="string", length=255)
     */
    private $tipo;

    /**
     * @ORM\ManyToOne(targetEntity="OrganizacionAlias", inversedBy="practicas")
     * @ORM\JoinColumn(name="organizacionalias_id", referencedColumnName="id", nullable=false)
     */
    private $organizacionAlias;
    
    /**
     * @ORM\ManyToOne(targetEntity="Contacto", inversedBy="practicas")
     * @ORM\JoinColumn(name="contacto_id", referencedColumnName="id", nullable=false)
     */
    private $contacto;
    
    /**
     * @ORM\ManyToOne(targetEntity="pDev\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="creador_id", referencedColumnName="id", nullable=false)
     */
    private $creador;

    /**
     * @ORM\OneToMany(targetEntity="AlumnoPracticante",mappedBy="practica")
     */
    private $practicantes;

    /**
     * @var string
     *
     * @ORM\Column(name="estado", type="string", length=255)
     */
    private $estado;
    
    /**
     * @var string
     *
     * @ORM\Column(name="estadoObservaciones", type="text",nullable=true)
     */
    private $estadoObservaciones;

        /**
     * @var string
     *
     * @ORM\Column(name="jornadas", type="string", length=255)
     */
    private $jornadas;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaInicio", type="datetime")
     */
    private $fechaInicio;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaTermino", type="datetime")
     */
    private $fechaTermino;

    /**
     * @var string
     *
     * @ORM\Column(name="manejoSoftware", type="string", length=255)
     */
    private $manejoSoftware;

    /**
     * @var string
     *
     * @ORM\Column(name="interes", type="string", length=255)
     */
    private $interes;

    /**
     * @var integer
     *
     * @ORM\Column(name="cupos", type="integer")
     */
    private $cupos;

    /**
     * @var string
     *
     * @ORM\Column(name="entrevista", type="string", length=255)
     */
    private $entrevista;

    /**
     * @var string
     *
     * @ORM\Column(name="remuneraciones", type="string", length=255)
     */
    private $remuneraciones;

    /**
     * @var string
     *
     * @ORM\Column(name="beneficios", type="string", length=255)
     */
    private $beneficios;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="text")
     */
    private $descripcion;


   
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->practicantes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->estado = Practica::ESTADO_PENDIENTE;
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
     * Set estado
     *
     * @param string $estado
     * @return Practica
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;
    
        return $this;
    }

    /**
     * Get estado
     *
     * @return string 
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Set estadoObservaciones
     *
     * @param string $estadoObservaciones
     * @return Practica
     */
    public function setEstadoObservaciones($estadoObservaciones)
    {
        $this->estadoObservaciones = $estadoObservaciones;
    
        return $this;
    }

    /**
     * Get estadoObservaciones
     *
     * @return string 
     */
    public function getEstadoObservaciones()
    {
        return $this->estadoObservaciones;
    }

    /**
     * Set jornadas
     *
     * @param string $jornadas
     * @return Practica
     */
    public function setJornadas($jornadas)
    {
        $this->jornadas = $jornadas;
    
        return $this;
    }

    /**
     * Get jornadas
     *
     * @return string 
     */
    public function getJornadas()
    {
        return $this->jornadas;
    }

    /**
     * Set fechaInicio
     *
     * @param \DateTime $fechaInicio
     * @return Practica
     */
    public function setFechaInicio($fechaInicio)
    {
        $this->fechaInicio = $fechaInicio;
    
        return $this;
    }

    /**
     * Get fechaInicio
     *
     * @return \DateTime 
     */
    public function getFechaInicio()
    {
        return $this->fechaInicio;
    }

    /**
     * Set fechaTermino
     *
     * @param \DateTime $fechaTermino
     * @return Practica
     */
    public function setFechaTermino($fechaTermino)
    {
        $this->fechaTermino = $fechaTermino;
    
        return $this;
    }

    /**
     * Get fechaTermino
     *
     * @return \DateTime 
     */
    public function getFechaTermino()
    {
        return $this->fechaTermino;
    }

    /**
     * Set manejoSoftware
     *
     * @param string $manejoSoftware
     * @return Practica
     */
    public function setManejoSoftware($manejoSoftware)
    {
        $this->manejoSoftware = $manejoSoftware;
    
        return $this;
    }

    /**
     * Get manejoSoftware
     *
     * @return string 
     */
    public function getManejoSoftware()
    {
        return $this->manejoSoftware;
    }

    /**
     * Set interes
     *
     * @param string $interes
     * @return Practica
     */
    public function setInteres($interes)
    {
        $this->interes = $interes;
    
        return $this;
    }

    /**
     * Get interes
     *
     * @return string 
     */
    public function getInteres()
    {
        return $this->interes;
    }

    /**
     * Set cupos
     *
     * @param integer $cupos
     * @return Practica
     */
    public function setCupos($cupos)
    {
        $this->cupos = $cupos;
    
        return $this;
    }

    /**
     * Get cupos
     *
     * @return integer 
     */
    public function getCupos()
    {
        return $this->cupos;
    }

    /**
     * Set entrevista
     *
     * @param string $entrevista
     * @return Practica
     */
    public function setEntrevista($entrevista)
    {
        $this->entrevista = $entrevista;
    
        return $this;
    }

    /**
     * Get entrevista
     *
     * @return string 
     */
    public function getEntrevista()
    {
        return $this->entrevista;
    }

    /**
     * Set remuneraciones
     *
     * @param string $remuneraciones
     * @return Practica
     */
    public function setRemuneraciones($remuneraciones)
    {
        $this->remuneraciones = $remuneraciones;
    
        return $this;
    }

    /**
     * Get remuneraciones
     *
     * @return string 
     */
    public function getRemuneraciones()
    {
        return $this->remuneraciones;
    }

    /**
     * Set beneficios
     *
     * @param string $beneficios
     * @return Practica
     */
    public function setBeneficios($beneficios)
    {
        $this->beneficios = $beneficios;
    
        return $this;
    }

    /**
     * Get beneficios
     *
     * @return string 
     */
    public function getBeneficios()
    {
        return $this->beneficios;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return Practica
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
     * Set organizacion
     *
     * @param \pDev\PracticasBundle\Entity\OrganizacionAlias $organizacionAlias
     * @return Practica
     */
    public function setOrganizacionAlias(\pDev\PracticasBundle\Entity\OrganizacionAlias $organizacionAlias)
    {
        $this->organizacionAlias = $organizacionAlias;
    
        return $this;
    }

    /**
     * Get organizacionAlias
     *
     * @return \pDev\PracticasBundle\Entity\OrganizacionAlias
     */
    public function getOrganizacionAlias()
    {
        return $this->organizacionAlias;
    }
    
    /**
     * Set contacto
     *
     * @param \pDev\PracticasBundle\Entity\Contacto $contacto
     * @return Practica
     */
    public function setContacto(\pDev\PracticasBundle\Entity\contacto $contacto)
    {
        $this->contacto = $contacto;
    
        return $this;
    }

    /**
     * Get contacto
     *
     * @return \pDev\PracticasBundle\Entity\Contacto
     */
    public function getContacto()
    {
        return $this->contacto;
    }
    
    /**
     * Set creador
     *
     * @param \pDev\UserBundle\Entity\Creador $creador
     * @return Practica
     */
    public function setCreador(\pDev\UserBundle\Entity\User $creador)
    {
        $this->creador = $creador;
    
        return $this;
    }

    /**
     * Get creador
     *
     * @return \pDev\UserBundle\Entity\User
     */
    public function getCreador()
    {
        return $this->creador;
    }

    /**
     * Add practicantes
     *
     * @param \pDev\PracticasBundle\Entity\AlumnoPracticante $practicantes
     * @return Practica
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