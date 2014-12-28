<?php

namespace pDev\PracticasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Organizacion
 *
 * @ORM\Table(name="nb_practicas_organizacion")
 * @ORM\Entity
 */
class Organizacion
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
     * @ORM\Column(name="rubro", type="string", length=255)
     */
    private $rubro;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="text")
     */
    private $descripcion;
    
    /**
     * @var string
     *
     * @ORM\Column(name="rut", type="string", length=255, nullable=true)
     */
    private $rut;

    /**
     * @ORM\OneToMany(targetEntity="OrganizacionAlias", mappedBy="organizacion")
     */
    private $aliases;

    /**
     * @var string
     *
     * @ORM\Column(name="pais", type="string", length=255)
     */
    private $pais;
    
    /**
     * @var string
     *
     * @ORM\Column(name="web", type="string", length=255)
     */
    private $web;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="personas", type="integer")
     */
    private $personasTotal;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="antiguedad", type="integer", nullable=true)     
     */
    private $antiguedad;

    /**
     * @ORM\ManyToMany(targetEntity="Contacto", mappedBy="organizaciones")
     */
    private $contactos;
    
    /**
     * @ORM\ManyToMany(targetEntity="Supervisor", mappedBy="organizaciones")
     */
    private $supervisores;
    
    /**
     * @ORM\ManyToOne(targetEntity="pDev\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="creador_id", referencedColumnName="id", nullable=false)
     */
    private $creador;
    
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
     * Set rubro
     *
     * @param string $rubro
     * @return Organizacion
     */
    public function setRubro($rubro)
    {
        $this->rubro = $rubro;
    
        return $this;
    }

    /**
     * Get rubro
     *
     * @return string 
     */
    public function getRubro()
    {
        return $this->rubro;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return Organizacion
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
     * Set pais
     *
     * @param string $pais
     * @return Organizacion
     */
    public function setPais($pais)
    {
        $this->pais = $pais;
    
        return $this;
    }

    /**
     * Get pais
     *
     * @return string 
     */
    public function getPais()
    {
        return $this->pais;
    }

    /**
     * Set web
     *
     * @param string $web
     * @return Organizacion
     */
    public function setWeb($web)
    {
        $this->web = $web;
    
        return $this;
    }

    /**
     * Get web
     *
     * @return string 
     */
    public function getWeb()
    {
        return $this->web;
    }

    /**
     * Set personasTotal
     *
     * @param integer $personasTotal
     * @return Organizacion
     */
    public function setPersonasTotal($personasTotal)
    {
        $this->personasTotal = $personasTotal;
    
        return $this;
    }

    /**
     * Get personasTotal
     *
     * @return integer 
     */
    public function getPersonasTotal()
    {
        return $this->personasTotal;
    }

    /**
     * Set antiguedad
     *
     * @param integer $antiguedad
     * @return Organizacion
     */
    public function setAntiguedad($antiguedad)
    {
        $this->antiguedad = $antiguedad;
    
        return $this;
    }

    /**
     * Get antiguedad
     *
     * @return integer 
     */
    public function getAntiguedad()
    {
        return $this->antiguedad;
    }

    /**
     * Set rut
     *
     * @param string $rut
     * @return Organizacion
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
     * Set tipo
     *
     * @param string $tipo
     * @return Organizacion
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
     * Add aliases
     *
     * @param \pDev\PracticasBundle\Entity\OrganizacionAlias $aliases
     * @return Organizacion
     */
    public function addAliase(\pDev\PracticasBundle\Entity\OrganizacionAlias $aliases)
    {
        $this->aliases[] = $aliases;
    
        return $this;
    }

    /**
     * Remove aliases
     *
     * @param \pDev\PracticasBundle\Entity\OrganizacionAlias $aliases
     */
    public function removeAliase(\pDev\PracticasBundle\Entity\OrganizacionAlias $aliases)
    {
        $this->aliases->removeElement($aliases);
    }

    /**
     * Get aliases
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAliases()
    {
        return $this->aliases;
    }
    
    /**
     * Set creador
     *
     * @param \pDev\UserBundle\Entity\Creador $creador
     * @return Organizacion
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
     * Add contactos
     *
     * @param \pDev\PracticasBundle\Entity\Contacto $contactos
     * @return Organizacion
     */
    public function addContacto(\pDev\PracticasBundle\Entity\Contacto $contactos)
    {
        $this->contactos[] = $contactos;
        $contactos->addOrganizacion($this);
    
        return $this;
    }

    /**
     * Remove contactos
     *
     * @param \pDev\PracticasBundle\Entity\Contacto $contactos
     */
    public function removeContacto(\pDev\PracticasBundle\Entity\Contacto $contactos)
    {
        $this->contactos->removeElement($contactos);
    }

    /**
     * Get contactos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getContactos()
    {
        return $this->contactos;
    }
    
    /**
     * Add supervisores
     *
     * @param \pDev\PracticasBundle\Entity\Supervisor $supervisores
     * @return Organizacion
     */
    public function addSupervisor(\pDev\PracticasBundle\Entity\Supervisor $supervisores)
    {
        $this->supervisores[] = $supervisores;
        $supervisores->addOrganizacion($this);
    
        return $this;
    }

    /**
     * Remove supervisores
     *
     * @param \pDev\PracticasBundle\Entity\Supervisor $supervisores
     */
    public function removeSupervisor(\pDev\PracticasBundle\Entity\Supervisor $supervisores)
    {
        $this->supervisores->removeElement($supervisores);
    }

    /**
     * Get supervisores
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSupervisores()
    {
        return $this->supervisores;
    }
    
    /**
     * Has contacto
     *
     * @param \pDev\PracticasBundle\Entity\Contacto $contactoBuscado
     * @return boolean 
     */
    public function hasContacto(\pDev\PracticasBundle\Entity\Contacto $contactoBuscado)
    {
        foreach($this->contactos as $contacto)
        {
            if($contacto === $contactoBuscado)
                return true;
        }
        return false;
    }
    
    /**
     * Has supervisor
     *
     * @param \pDev\PracticasBundle\Entity\Supervisor $supervisorBuscado
     * @return boolean 
     */
    public function hasSupervisor(\pDev\PracticasBundle\Entity\Supervisor $supervisorBuscado)
    {
        foreach($this->supervisores as $supervisor)
        {
            if($supervisor === $supervisorBuscado)
                return true;
        }
        return false;
    }
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->aliases = new \Doctrine\Common\Collections\ArrayCollection();
        $this->contactos = new \Doctrine\Common\Collections\ArrayCollection();
        $this->supervisores = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function __toString() 
    {
        return count($this->aliases)>0?$this->aliases[0]->getNombre():'sin nombre';
    }
}
