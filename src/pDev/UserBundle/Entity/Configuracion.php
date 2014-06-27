<?php

namespace pDev\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Configuracion
 *
 * @ORM\Table(name="nb_user_configuracion")
 * @ORM\Entity
 */
class Configuracion
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
     * @ORM\ManyToOne(targetEntity="Sitio", inversedBy="configuraciones")
     * @ORM\JoinColumn(name="sitio_id", referencedColumnName="id", nullable=false)
     */
    private $sitio;

    /**
     * @var string
     *
     * @ORM\Column(name="keyname", type="string")
     */
    private $keyName;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string")
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string")
     */
    private $descripcion;

    /**
     * @var string
     *
     * @ORM\Column(name="valor", type="text")
     */
    private $valor;

    /**
     * @var string
     *
     * @ORM\Column(name="valorDefault", type="text")
     */
    private $valorDefault;
    
    /**
     * @var string
     *
     * @ORM\Column(name="valorTipo", type="string")
     */
    private $valorTipo;


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
     * Set keyName
     *
     * @param string $keyName
     * @return Configuracion
     */
    public function setKeyName($keyName)
    {
        $this->keyName = $keyName;
    
        return $this;
    }

    /**
     * Get keyName
     *
     * @return string 
     */
    public function getKeyName()
    {
        return $this->keyName;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return Configuracion
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
     * @return Configuracion
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
     * Set valor
     *
     * @param string $valor
     * @return Configuracion
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
        if($this->valorTipo=="datetime")
            return new \DateTime($this->valor);
        else if($this->valorTipo=="integer")
            return intval($this->valor);
        return $this->valor;
    }
    
    /**
     * Get valor
     *
     * @return string 
     */
    public function getValorPlano()
    {
        return $this->valor;
    }

    /**
     * Set valorDefault
     *
     * @param string $valorDefault
     * @return Configuracion
     */
    public function setValorDefault($valorDefault)
    {
        $this->valorDefault = $valorDefault;
    
        return $this;
    }

    /**
     * Get valorDefault
     *
     * @return string 
     */
    public function getValorDefault()
    {
        return $this->valorDefault;
    }
    
    /**
     * Set valorTipo
     *
     * @param string $valorTipo
     * @return Configuracion
     */
    public function setValorTipo($valorTipo)
    {
        $this->valorTipo = $valorTipo;
    
        return $this;
    }

    /**
     * Get valorTipo
     *
     * @return string 
     */
    public function getValorTipo()
    {
        return $this->valorTipo;
    }

    /**
     * Set sitio
     *
     * @param \pDev\UserBundle\Entity\Sitio $sitio
     * @return Configuracion
     */
    public function setSitio(\pDev\UserBundle\Entity\Sitio $sitio)
    {
        $this->sitio = $sitio;
    
        return $this;
    }

    /**
     * Get sitio
     *
     * @return \pDev\UserBundle\Entity\Sitio
     */
    public function getSitio()
    {
        return $this->sitio;
    }
}
