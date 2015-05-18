<?php

namespace pDev\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Persona
 *
 * @ORM\Table(name="nb_user_persona")
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discriminator", type="string")
 * @ORM\DiscriminatorMap({
 *      "alumno" = "pDev\UserBundle\Entity\Alumno",
 *      "profesor" = "pDev\UserBundle\Entity\Profesor",
 *      "funcionario" = "pDev\UserBundle\Entity\Funcionario",
 *      "practica_contacto" = "pDev\PracticasBundle\Entity\Contacto",
 *      "practica_supervisor" = "pDev\PracticasBundle\Entity\Supervisor",
 * })
 */
class Persona
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
     * @ORM\ManyToOne(targetEntity="User", inversedBy="personas", cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     */
    private $usuario;

    /**
     * @var string
     *
     * @ORM\Column(name="nombres", type="string")
     */
    private $nombres;

    /**
     * @var string
     *
     * @ORM\Column(name="apellidoPaterno", type="string")
     */
    private $apellidoPaterno;

    /**
     * @var string
     *
     * @ORM\Column(name="apellidoMaterno", type="string")
     */
    private $apellidoMaterno;

    /**
     * @var string
     *
     * @ORM\Column(name="rut", type="string", length=20, nullable=true)
     */
    protected $rut;
    
    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", nullable=true)
     */
    private $email;

    /**
     * @ORM\OneToMany(targetEntity="Telefono", mappedBy="persona")
     */
    private $telefonos;
    
    /**
     * @ORM\OneToMany(targetEntity="Direccion", mappedBy="persona")
     */
    private $direccion;
    
    /**
     * @var string
     *
     * @ORM\Column(name="tipo", type="string")
     */
    protected $tipo;
    
    /**
     * @var string
     *
     * @ORM\Column(name="emailSecundario", type="string",nullable=true)
     */
    private $emailSecundario;
    
    /**
     * @ORM\OneToOne(targetEntity="Archivo")
     * @ORM\JoinColumn(name="foto_id", referencedColumnName="id",nullable=true)
     */
    private $foto;
    
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
     * Set usuario
     *
     * @param User $usuario
     * @return Persona
     */
    public function setUsuario(User $usuario = null)
    {
        $this->usuario = $usuario;
    
        return $this;
    }

    /**
     * Get usuario
     *
     * @return User 
     */
    public function getUsuario()
    {
        return $this->usuario;
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
     * @return Alumno
     */
    public function setRut($rut)
    {
        $rut = str_replace("-","",$rut);    // Eliminamos los guiones
        $rut = str_replace(".","",$rut);    // Eliminamos los puntos
        $rut = str_replace(",","",$rut);    // Eliminamos las comas
        $rut = str_replace(" ","",$rut);    // Eliminamos los espacios
        $rut = strtoupper($rut);            // Mayúsculas para las K
        
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
     * Get rut sin formato
     *
     * @return string 
     */
    public function getRutSinFormato()
    {
        return preg_replace("/[^Kk0-9]/",'',$this->rut);
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
     * Set telefono
     *
     * @param string $telefono
     * @return Alumno
     */
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;
    
        return $this;
    }

    /**
     * Get telefono
     *
     * @return string 
     */
    public function getTelefono()
    {
        return $this->telefono;
    }
        
    /**
     * Add telefonos
     *
     * @param \pDev\UserBundle\Entity\Telefono $telefonos
     * @return Persona
     */
    public function addTelefono(\pDev\UserBundle\Entity\Telefono $telefonos)
    {
        $this->telefonos[] = $telefonos;
    
        return $this;
    }

    /**
     * Remove telefonos
     *
     * @param \pDev\UserBundle\Entity\Telefono $telefonos
     */
    public function removeTelefono(\pDev\UserBundle\Entity\Telefono $telefonos)
    {
        $this->telefonos->removeElement($telefonos);
    }

    /**
     * Get telefonos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTelefonos()
    {
        return $this->telefonos;
    }

    /**
     * Add direccion
     *
     * @param \pDev\UserBundle\Entity\Direccion $direccion
     * @return Persona
     */
    public function addDireccion(\pDev\UserBundle\Entity\Direccion $direccion)
    {
        $this->direccion[] = $direccion;
    
        return $this;
    }

    /**
     * Remove direccion
     *
     * @param \pDev\UserBundle\Entity\Direccion $direccion
     */
    public function removeDireccion(\pDev\UserBundle\Entity\Direccion $direccion)
    {
        $this->direccion->removeElement($direccion);
    }

    /**
     * Get direccion
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDireccion()
    {
        return $this->direccion;
    }
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->telefonos = new \Doctrine\Common\Collections\ArrayCollection();
        $this->direccion = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    public function __toString()
    {
        return "".$this->getNombreCompleto();
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
     * Set foto
     *
     * @param Archivo $foto
     * @return Persona
     */
    public function setFoto(Archivo $foto)
    {
        $this->foto = $foto;
    
        return $this;
    }

    /**
     * Get foto
     *
     * @return Archivo
     */
    public function getFoto()
    {
        return $this->foto;
    }
    
    /**
     * Set emailSecundario
     *
     * @param string $emailSecundario
     * @return Alumno
     */
    public function setEmailSecundario($emailSecundario)
    {
        $this->emailSecundario = $emailSecundario;
    
        return $this;
    }

    /**
     * Get emailSecundario
     *
     * @return string 
     */
    public function getEmailSecundario()
    {
        return $this->emailSecundario;
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
