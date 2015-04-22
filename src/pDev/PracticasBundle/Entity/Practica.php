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
    const ESTADO_REVISION = "estado.revision";
    const ESTADO_PENDIENTE = "estado.pendiente";
    const ESTADO_APROBADA = "estado.aprobada";
    const ESTADO_RECHAZADA = "estado.rechazada";
    const ESTADO_FINALIZADA = "estado.finalizada";
    
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
     * @ORM\Column(name="tipo", type="string", length=255, nullable=true)
     */
    private $tipo;

    /**
     * @ORM\ManyToOne(targetEntity="Organizacion", inversedBy="practicas")
     * @ORM\JoinColumn(name="organizacion_id", referencedColumnName="id", nullable=false)
     */
    private $organizacion;
    
    /**
     * @ORM\ManyToOne(targetEntity="Contacto", inversedBy="practicas")
     * @ORM\JoinColumn(name="contacto_id", referencedColumnName="id", nullable=false)
     */
    private $contacto;
    
    /**
     * @ORM\ManyToOne(targetEntity="Supervisor", inversedBy="practicas")
     * @ORM\JoinColumn(name="supervisor_id", referencedColumnName="id", nullable=true)
     */
    private $supervisor;
    
    /**
     * @ORM\ManyToOne(targetEntity="pDev\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="creador_id", referencedColumnName="id", nullable=false)
     */
    private $creador;

    /**
     * @ORM\OneToMany(targetEntity="AlumnoPracticante", mappedBy="practica")
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
     * @ORM\Column(name="fechaTermino", type="datetime", nullable=true)
     */
    private $fechaTermino;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="duracionCantidad", type="integer")
     */
    private $duracionCantidad;
    
    /**
     * @var string
     *
     * @ORM\Column(name="duracionUnidad", type="string")
     */
    private $duracionUnidad;
    
    /**
     * @var string
     *
     * @ORM\Column(name="manejoSoftware", type="string", length=255, nullable=true)
     */
    private $manejoSoftware;

    /**
     * @var string
     *
     * @ORM\Column(name="interes", type="string", length=255, nullable=true)
     */
    private $interes;

    /**
     * @var integer
     *
     * @ORM\Column(name="cupos", type="integer")
     */
    private $cupos = 1;

    /**
     * @var string
     *
     * @ORM\Column(name="entrevista", type="boolean", nullable=true)
     */
    private $entrevista;

    /**
     * @var string
     *
     * @ORM\Column(name="remuneraciones", type="string", length=255, nullable=true)
     */
    private $remuneraciones;

    /**
     * @var string
     *
     * @ORM\Column(name="beneficios", type="string", length=255, nullable=true)
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
        $this->estado = Practica::ESTADO_REVISION;
    }
    
    /**
     * Get tipo
     *
     * @return string 
     */
    public function __toString()
    {
        return "".$this->organizacion." - ".$this->tipo;
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
     * Set nombre
     *
     * @param string $nombre
     * @return Practica
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
     * Set duracionCantidad
     *
     * @param integer $duracionCantidad
     * @return Practica
     */
    public function setDuracionCantidad($duracionCantidad)
    {
        $this->duracionCantidad = $duracionCantidad;
    
        return $this;
    }

    /**
     * Get duracionCantidad
     *
     * @return integer
     */
    public function getDuracionCantidad()
    {
        return $this->duracionCantidad;
    }
    
    /**
     * Set duracionUnidad
     *
     * @param string $duracionUnidad
     * @return Practica
     */
    public function setDuracionUnidad($duracionUnidad)
    {
        $this->duracionUnidad = $duracionUnidad;
    
        return $this;
    }

    /**
     * Get duracionUnidad
     *
     * @return string
     */
    public function getDuracionUnidad()
    {
        return $this->duracionUnidad;
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
     * @param boolean $entrevista
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
     * @return boolean 
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
     * @param \pDev\PracticasBundle\Entity\Organizacion $organizacion
     * @return Practica
     */
    public function setOrganizacion(\pDev\PracticasBundle\Entity\Organizacion $organizacion)
    {
        $this->organizacion = $organizacion;
    
        return $this;
    }

    /**
     * Get organizacion
     *
     * @return \pDev\PracticasBundle\Entity\Organizacion
     */
    public function getOrganizacion()
    {
        return $this->organizacion;
    }
    
    /**
     * Set contacto
     *
     * @param \pDev\PracticasBundle\Entity\Contacto $contacto
     * @return Practica
     */
    public function setContacto(\pDev\PracticasBundle\Entity\Contacto $contacto)
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
     * Set supervisor
     *
     * @param \pDev\PracticasBundle\Entity\Supervisor $supervisor
     * @return Practica
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
    
    
    public function getPostulantes()
    {
        return $this->practicantes->filter(
            function($entry) {
               return $entry->getEstado() === AlumnoPracticante::ESTADO_POSTULADO;
            }
        ); 
    }
    
    public function getAceptados()
    {
        return $this->practicantes->filter(
            function($entry) {
               return $entry->getEstado() !== AlumnoPracticante::ESTADO_POSTULADO;
            }
        );   
    }
    
    public function isPostulado(\pDev\UserBundle\Entity\Alumno $p)
    {
        foreach($this->getPostulantes() as $postulante)
        {
            if($postulante->getAlumno() === $p)
                return true;
        }
        return false;
    }
    
    public function isAceptado(\pDev\UserBundle\Entity\Alumno $a)
    {
        foreach($this->getAceptados() as $aceptado)
        {
            if($aceptado->getAlumno() === $a)
                return true;
        }
        return false;
    }
    
    public function hasContacto($user)
    {
        if($this->contacto === $user)
            return true;
        else
            return false;
    }
    
    public function hasSupervisor($user)
    {
        if($this->supervisor === $user)
            return true;
        else
            return false;
    }
    
    public function getEstadoLabel()
    {
        if($this->estado === Practica::ESTADO_REVISION)
            return "En revisión";
        elseif($this->estado === Practica::ESTADO_PENDIENTE)
            return "enviada a revisión";
        elseif($this->estado === Practica::ESTADO_APROBADA)
            return "aprobada";
        elseif($this->estado === Practica::ESTADO_RECHAZADA)
            return "rechazada";
        elseif($this->estado === Practica::ESTADO_FINALIZADA)
            return "asignada";
        
        return '';
    }
}
