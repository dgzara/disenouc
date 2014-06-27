<?php

namespace pDev\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Notificacion
 *
 * @ORM\Table(name="nb_user_notificacion")
 * @ORM\Entity
 */
class Notificacion
{
    const BROADCAST_NOTICE = 'broadcast.notice';
    const BROADCAST_REPORT = 'broadcast.report';
    
    const USER_NOTICE = 'user.notice';
    const USER_INFO = 'user.information';
    const USER_ERROR = 'user.error';
    const USER_SUCCESS = 'user.success';
    const USER_FORBIDDEN = 'user.forbidden';
    const USER_ALERT = 'user.alert';
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User",inversedBy="notificaciones")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     */
    private $user;
    
    /**
    * @ORM\Column(type="datetime",nullable=true)
    */
    private $created;
    
    /**
    * @ORM\Column(type="string")
    */
    private $mensaje;
    
    /**
     * @ORM\OneToMany(targetEntity="NotificacionLeida",mappedBy="notificacion")
     */
    private $leidos;
    
    /**
    * @ORM\Column(type="string")
    */
    private $llave;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->llave = 'notify';
        $this->created = new \DateTime();
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
     * Set created
     *
     * @param \DateTime $created
     * @return Notificacion
     */
    public function setCreated($created)
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
     * Set mensaje
     *
     * @param string $mensaje
     * @return Notificacion
     */
    public function setMensaje($mensaje)
    {
        $this->mensaje = $mensaje;
    
        return $this;
    }

    /**
     * Get mensaje
     *
     * @return string 
     */
    public function getMensaje()
    {
        return $this->mensaje;
    }
    
    /**
     * Set key
     *
     * @param string $key
     * @return Notificacion
     */
    public function setLlave($key)
    {
        $this->llave = $key;
    
        return $this;
    }

    /**
     * Get key
     *
     * @return string 
     */
    public function getLlave()
    {
        return $this->llave;
    }

    /**
     * Set user
     *
     * @param \pDev\UserBundle\Entity\User $user
     * @return Notificacion
     */
    public function setUser(\pDev\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return \pDev\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Add leidos
     *
     * @param \pDev\UserBundle\Entity\NotificacionLeida $leidos
     * @return Notificacion
     */
    public function addLeido(\pDev\UserBundle\Entity\NotificacionLeida $leidos)
    {
        $this->leidos[] = $leidos;
    
        return $this;
    }

    /**
     * Remove leidos
     *
     * @param \pDev\UserBundle\Entity\NotificacionLeida $leidos
     */
    public function removeLeido(\pDev\UserBundle\Entity\NotificacionLeida $leidos)
    {
        $this->leidos->removeElement($leidos);
    }

    /**
     * Get leidos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLeidos()
    {
        return $this->leidos;
    }
}