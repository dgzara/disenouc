<?php

namespace pDev\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sitio
 *
 * @ORM\Table(name="nb_user_sitios")
 * @ORM\Entity
 */
class Sitio
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
     * @ORM\Column(name="site", type="string")
     */
    protected $site;
    
    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string")
     */
    private $nombre;
    
    /**
     * @ORM\OneToMany(targetEntity="Configuracion", mappedBy="sitio")
     */
    private $configuraciones;

    
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
     * Set site
     *
     * @param string $site
     * @return Sitio
     */
    public function setSite($site)
    {
        $this->site = $site;
    
        return $this;
    }

    /**
     * Get site
     *
     * @return string 
     */
    public function getSite()
    {
        return $this->site;
    }
    
    /**
     * Set nombre
     *
     * @param string $nombre
     * @return Sitio
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
     * Constructor
     */
    public function __construct()
    {
        $this->configuraciones = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add configuraciones
     *
     * @param \pDev\UserBundle\Entity\Configuracion $configuraciones
     * @return ConfiguracionCategoria
     */
    public function addConfiguracione(\pDev\UserBundle\Entity\Configuracion $configuraciones)
    {
        $this->configuraciones[] = $configuraciones;
    
        return $this;
    }

    /**
     * Remove configuraciones
     *
     * @param \pDev\UserBundle\Entity\Configuracion $configuraciones
     */
    public function removeConfiguracione(\pDev\UserBundle\Entity\Configuracion $configuraciones)
    {
        $this->configuraciones->removeElement($configuraciones);
    }

    /**
     * Get configuraciones
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getConfiguraciones()
    {
        return $this->configuraciones;
    }
}
