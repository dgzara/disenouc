<?php
// src/pDev/UserBundle/Entity/User.php

namespace pDev\UserBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="nb_fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\OneToMany(targetEntity="Persona", mappedBy="usuario")
     */
    private $personas;
    
    /**
     * @ORM\OneToOne(targetEntity="Archivo")
     * @ORM\JoinColumn(name="foto_id", referencedColumnName="id",nullable=true)
     */
    private $foto;
    
    /**
     * @ORM\OneToMany(targetEntity="Permiso", mappedBy="user",cascade="persist")
     */
    private $permisos;
    
    /**
     * @ORM\OneToMany(targetEntity="Notificacion",mappedBy="user")
     */
    private $notificaciones;
    
    /**
    * @ORM\Column(type="datetime")
    */
    private $created;
    
    /**
    * @ORM\Column(type="boolean")
    */
    private $external;
    
    /**
    * @ORM\Column(name="previous_login",type="datetime",nullable=true)
    */
    private $previousLogin;
    
    /**
     * @var string
     *
     * @ORM\Column(name="nombres", type="string", nullable=true)
     */
    private $nombres;

    /**
     * @var string
     *
     * @ORM\Column(name="apellidoPaterno", type="string", nullable=true)
     */
    private $apellidoPaterno;

    /**
     * @var string
     *
     * @ORM\Column(name="apellidoMaterno", type="string", nullable=true)
     */
    private $apellidoMaterno;

    /**
     * @var string
     *
     * @ORM\Column(name="rut", type="string", length=9, nullable=true)
     */
    protected $rut;
    
    /**
     * @var string
     *
     * @ORM\Column(name="telefono", type="string", length=255, nullable=true)
     */
    private $numeroTelefono;
    
    /**
     * @var string
     *
     * @ORM\Column(name="direccionCalle", type="string", length=255, nullable=true)
     */
    private $direccionCalle;
    
    public function __construct()
    {
        parent::__construct();
        // your own logic
        $this->personas = new \Doctrine\Common\Collections\ArrayCollection();
        $this->permisos = new \Doctrine\Common\Collections\ArrayCollection();
        $this->notificaciones = new \Doctrine\Common\Collections\ArrayCollection();
        $this->created = new \DateTime();
        $this->external = false;
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
     * has persona
     *
     * @return boolean
     */
    public function hasPersona($tipo)
    {
        foreach($this->personas as $persona)
        {
            if($persona->getTipo()===$tipo)
                return true;
        }
        
        return false;
    }
    
    /**
     * has rut
     *
     * @return boolean
     */
    public function hasRut($rut)
    {
        foreach($this->personas as $persona)
        {
            if($persona->getRut()===$rut)
                return true;
        }
        
        return false;
    }
    
    /**
     * get persona
     *
     * @return boolean
     */
    public function getPersona($tipo)
    {
        foreach($this->personas as $persona)
        {
            if($persona->getTipo()===$tipo)
                return $persona;
        }
        
        return null;
    }
    
    /**
     * Add personas
     *
     * @param \pDev\UserBundle\Entity\Persona $personas
     * @return Persona
     */
    public function addPersona(\pDev\UserBundle\Entity\Persona $personas)
    {
        $this->personas[] = $personas;
    
        return $this;
    }

    /**
     * Remove personas
     *
     * @param \pDev\UserBundle\Entity\Persona $personas
     */
    public function removePersona(\pDev\UserBundle\Entity\Persona $personas)
    {
        $this->personas->removeElement($personas);
    }

    /**
     * Get personas
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPersonas($tipo = null)
    {
        if($tipo)
        {
            $return = array();
        
            foreach($this->personas as $persona)
            {
                if($persona->getTipo()===$tipo)
                    $return[] = $persona;
            }

            return $return;
        }
        
        return $this->personas;
    }
    
    /**
     * Add permiso
     *
     * @param \pDev\UserBundle\Entity\Permiso $permiso
     * @return User
     */
    public function addPermiso(\pDev\UserBundle\Entity\Permiso $permiso)
    {
        $this->permisos[] = $permiso;
    
        return $this;
    }

    /**
     * Remove permiso
     *
     * @param \pDev\UserBundle\Entity\Permiso $permiso
     */
    public function removePermiso(\pDev\UserBundle\Entity\Permiso $permiso)
    {
        $this->permisos->removeElement($permiso);
    }

    /**
     * Get permisos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPermisos()
    {
        return $this->permisos;
    }
    
    /**
     * Add notificacion
     *
     * @param \pDev\UserBundle\Entity\Notificacion $notificaciones
     * @return User
     */
    public function addNotificacion(\pDev\UserBundle\Entity\Notificacion $notificaciones)
    {
        $this->notificaciones[] = $notificaciones;
    
        return $this;
    }

    /**
     * Remove notificacion
     *
     * @param \pDev\UserBundle\Entity\Notificacion $notificaciones
     */
    public function removeNotificacion(\pDev\UserBundle\Entity\Notificacion $notificaciones)
    {
        $this->notificaciones->removeElement($notificaciones);
    }

    /**
     * Get notificaciones
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getNotificaciones()
    {
        return $this->notificaciones;
    }
    
    /**
     * Get tipos
     *
     * @return array 
     */
    public function getTipos()
    {
        $array = array();
        foreach($this->personas as $persona)
            $array[] = $persona->getTipo();
        return $array;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Persona
     */
    public function setEmail($email)
    {
        $this->email = $email;
    
        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }
    
    /**
     * Set foto
     *
     * @param Foto $foto
     * @return User
     */
    public function setFoto(Foto $foto=null)
    {
        $this->foto = $foto;
    
        return $this;
    }

    /**
     * Get foto
     *
     * @return Foto
     */
    public function getFoto()
    {
        return $this->foto;
    }
    
    /**
     * Set created
     *
     * @param \DateTime $created
     * @return User
     */
    public function setCreated($create)
    {
        $this->created = $created;
    
        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set external
     *
     * @param boolean $external
     * @return User
     */
    public function setExternal($external)
    {
        $this->external = $external;
    
        return $this;
    }

    /**
     * Get external
     *
     * @return boolean
     */
    public function getExternal()
    {
        return $this->external;
    }
    
    /**
     * Set previousLogin
     *
     * @param \DateTime $previousLogin
     * @return User
     */
    public function setPreviousLogin($previousLogin)
    {
        $this->previousLogin = $previousLogin;
    
        return $this;
    }

    /**
     * Get previousLogin
     *
     * @return \DateTime 
     */
    public function getPreviousLogin()
    {
        return $this->previousLogin;
    }

    /**
     * Add notificaciones
     *
     * @param \pDev\UserBundle\Entity\Notificacion $notificaciones
     * @return User
     */
    public function addNotificacione(\pDev\UserBundle\Entity\Notificacion $notificaciones)
    {
        $this->notificaciones[] = $notificaciones;
    
        return $this;
    }

    /**
     * Remove notificaciones
     *
     * @param \pDev\UserBundle\Entity\Notificacion $notificaciones
     */
    public function removeNotificacione(\pDev\UserBundle\Entity\Notificacion $notificaciones)
    {
        $this->notificaciones->removeElement($notificaciones);
    }
    
    /**
     * Set nombres
     *
     * @param string $nombres
     * @return Persona
     */
    public function setNombres($nombres)
    {
        $this->nombres = $nombres;
    
        return $this;
    }

    /**
     * Get nombres
     *
     * @return string 
     */
    public function getNombres()
    {
        return $this->nombres;
    }

    /**
     * Set apellidoPaterno
     *
     * @param string $apellidoPaterno
     * @return Persona
     */
    public function setApellidoPaterno($apellidoPaterno)
    {
        $this->apellidoPaterno = $apellidoPaterno;
    
        return $this;
    }
    

    /**
     * Get apellidoPaterno
     *
     * @return string 
     */
    public function getApellidoPaterno()
    {
        return $this->apellidoPaterno;
    }

    /**
     * Set apellidoMaterno
     *
     * @param string $apellidoMaterno
     * @return Persona
     */
    public function setApellidoMaterno($apellidoMaterno)
    {
        $this->apellidoMaterno = $apellidoMaterno;
    
        return $this;
    }

    /**
     * Get apellidoMaterno
     *
     * @return string 
     */
    public function getApellidoMaterno()
    {
        return $this->apellidoMaterno;
    }
    
    /**
     * Get nombreCompleto
     *
     * @return string 
     */
    public function getNombreCompleto()
    {
        return $this->getNombres().' '.$this->getApellidoPaterno().' '.$this->getApellidoMaterno();
    }

    /**
     * Set rut
     *
     * @param string $rut
     * @return User
     */
    public function setRut($rut)
    {
        $this->rut = $rut;
    
        return $this;
    }

    /**
     * Get rut
     *
     * @return string 
     */
    public function getRut()
    {
        return $this->rut;
    }
    
    /**
     * Set numeroTelefono
     *
     * @param string $numeroTelefono
     * @return User
     */
    public function setNumeroTelefono($numeroTelefono)
    {
        $this->numeroTelefono = $numeroTelefono;
    
        return $this;
    }

    /**
     * Get numeroTelefono
     *
     * @return string 
     */
    public function getNumeroTelefono()
    {
        return $this->numeroTelefono;
    }
    
    /**
     * Set direccionCalle
     *
     * @param string $direccionCalle
     * @return User
     */
    public function setDireccionCalle($direccionCalle)
    {
        $this->direccionCalle = $direccionCalle;
    
        return $this;
    }

    /**
     * Get direccionCalle
     *
     * @return string 
     */
    public function getDireccionCalle()
    {
        return $this->direccionCalle;
    }
}