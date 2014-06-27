<?php

namespace pDev\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Direccion
 *
 * @ORM\Table(name="nb_persona_direccion")
 * @ORM\Entity
 */
class Direccion
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
     * @ORM\Column(name="calleNombre", type="string")
     */
    private $calleNombre;

    /**
     * @var string
     *
     * @ORM\Column(name="calleNumero", type="string")
     */
    private $calleNumero;

    /**
     * @var string
     *
     * @ORM\Column(name="dptoNumero", type="string")
     */
    private $dptoNumero;

    /**
     * @var string
     *
     * @ORM\Column(name="villa", type="string")
     */
    private $villa;

    /**
     * @var string
     *
     * @ORM\Column(name="comuna", type="string")
     */
    private $comuna;

    /**
     * @var string
     *
     * @ORM\Column(name="ciudad", type="string",nullable=true)
     */
    private $ciudad;

    /**
     * @ORM\ManyToOne(targetEntity="Persona", inversedBy="direccion")
     * @ORM\JoinColumn(name="persona_id", referencedColumnName="id", nullable=false)
     */
    private $persona;
    
    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string")
     */
    private $descripcion;
    
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
     * Set calleNombre
     *
     * @param string $calleNombre
     * @return Direccion
     */
    public function setCalleNombre($calleNombre)
    {
        $this->calleNombre = $calleNombre;
    
        return $this;
    }

    /**
     * Get calleNombre
     *
     * @return string 
     */
    public function getCalleNombre()
    {
        return $this->calleNombre;
    }

    /**
     * Set calleNumero
     *
     * @param string $calleNumero
     * @return Direccion
     */
    public function setCalleNumero($calleNumero)
    {
        $this->calleNumero = $calleNumero;
    
        return $this;
    }

    /**
     * Get calleNumero
     *
     * @return string 
     */
    public function getCalleNumero()
    {
        return $this->calleNumero;
    }

    /**
     * Set dptoNumero
     *
     * @param string $dptoNumero
     * @return Direccion
     */
    public function setDptoNumero($dptoNumero)
    {
        $this->dptoNumero = $dptoNumero;
    
        return $this;
    }

    /**
     * Get dptoNumero
     *
     * @return string 
     */
    public function getDptoNumero()
    {
        return $this->dptoNumero;
    }

    /**
     * Set villa
     *
     * @param string $villa
     * @return Direccion
     */
    public function setVilla($villa)
    {
        $this->villa = $villa;
    
        return $this;
    }

    /**
     * Get villa
     *
     * @return string 
     */
    public function getVilla()
    {
        return $this->villa;
    }

    /**
     * Set comuna
     *
     * @param string $comuna
     * @return Direccion
     */
    public function setComuna($comuna)
    {
        $this->comuna = $comuna;
    
        return $this;
    }

    /**
     * Get comuna
     *
     * @return string 
     */
    public function getComuna()
    {
        return $this->comuna;
    }

    /**
     * Set ciudad
     *
     * @param string $ciudad
     * @return Direccion
     */
    public function setCiudad($ciudad)
    {
        $this->ciudad = $ciudad;
    
        return $this;
    }

    /**
     * Get ciudad
     *
     * @return string 
     */
    public function getCiudad()
    {
        return $this->ciudad;
    }

    /**
     * Set persona
     *
     * @param Persona $persona
     * @return Direccion
     */
    public function setPersona(Persona $persona)
    {
        $this->persona = $persona;
    
        return $this;
    }

    /**
     * Get persona
     *
     * @return Persona 
     */
    public function getPersona()
    {
        return $this->persona;
    }
    
    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return Telefono
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
     * Get completa
     *
     * @return string 
     */
    public function getCompleta()
    {
        $return = "";
        if($this->calleNombre and trim($this->calleNombre) != '-')
            $return .= $this->calleNombre;
        if($this->calleNumero and trim($this->calleNumero) != '-')
            $return .= ' '.$this->calleNumero;
        if($this->dptoNumero and trim($this->dptoNumero) != '-')
            $return .= ' Dpto. '.$this->dptoNumero;
        if(trim($this->villa)!="")
        {
            if($return!="")
                $return .= ', ';
            $return .= $this->villa;
        }
        if(trim($this->comuna)!="")
        {
            if($return!="")
                $return .= ', ';
            $return .= $this->comuna;
        }
        if(trim($this->ciudad)!="")
        {
            if($return!="")
                $return .= ', ';
            $return .= $this->ciudad;
        }
        return trim($return);
    }
}
