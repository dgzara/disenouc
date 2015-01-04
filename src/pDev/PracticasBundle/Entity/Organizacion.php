<?php

namespace pDev\PracticasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Organizacion
 *
 * @ORM\Table(name="nb_practicas_organizacion")
 * @ORM\HasLifecycleCallbacks 
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
     * @ORM\ManyToMany(targetEntity="Supervisor", mappedBy="organizaciones", cascade={"persist"})
     */
    private $supervisores;
    
    /**
     * @ORM\ManyToOne(targetEntity="pDev\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="creador_id", referencedColumnName="id", nullable=true)
     */
    private $creador;
    
    /**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $path;
	
	/**
     * @Assert\Image(maxSize="6000000")
     */
    private $profilePic;
    
    /**
	 * @ORM\Column(type="boolean", nullable=true)
	 */
	private $isFileChanged;
	
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
     * Set path
     *
     * @param string $path
     * @return Organizacion
     */
    public function setPath($path)
    {
        $this->path = $path;
    
        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set isFileChanged
     *
     * @param boolean $isFileChanged
     * @return Organizacion
     */
    public function setIsFileChanged($isFileChanged)
    {
        $this->isFileChanged = $isFileChanged;
    
        return $this;
    }

    /**
     * Get isFileChanged
     *
     * @return boolean 
     */
    public function getIsFileChanged()
    {
        return $this->isFileChanged;
    }
    
    /**
     * Get absolute path
     *
     * @return string 
     */
    public function getAbsolutePath()
    {
        return null === $this->path ? $this->getImageRootDir().'/no-image.jpg' : $this->getUploadRootDir().'/'.$this->path;
    }

    /**
     * Get web path
     *
     * @return string 
     */
    public function getWebPath()
    {
        return null === $this->path ? $this->getImageDir().'/no-image.jpg' : $this->getUploadDir().'/'.$this->path;
    }

    /**
     * Get upload root dir
     *
     * @return string 
     */
    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    /**
     * Get image root dir
     *
     * @return string 
     */    
    protected function getImageRootDir()
    {
        return __DIR__.'/../../../../web/'.$this->getImageDir();
    }

    /**
     * Get image dir
     *
     * @return string 
     */
    protected function getImageDir()
    {
        // get rid of the __DIR__ so it doesn't screw when displaying uploaded doc/image in the view.
        return 'img';
    }
    
    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw when displaying uploaded doc/image in the view.
        return 'uploads/organizacion/pics';
    }
    
    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->profilePic) {
            // do whatever you want to generate a unique name
            $this->path = uniqid().'.'.$this->profilePic->guessExtension();
        }
        $this->isFileChanged=false;
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->profilePic) {
            return;
        }

        // if there is an error when moving the file, an exception will
        // be automatically thrown by move(). This will properly prevent
        // the entity from being persisted to the database on error
        $this->profilePic->move($this->getUploadRootDir(), $this->path);

        unset($this->profilePic);
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if ($profilePic = $this->getAbsolutePath()) {
            unlink($profilePic);
        }
    }

    /**
     * Get profile pic
     *
     */
    public function getProfilePic()
    {
        return $this->profilePic;
    }
    
    /**
     * Set profile pic
     *
     * @param File $file
     */
    public function setProfilePic($var)
    {
        $this->profilePic = $var;
    }
}
