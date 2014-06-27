<?php

namespace pDev\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NotificacionLeida
 *
 * @ORM\Table(name="nb_user_notificacion_leida")
 * @ORM\Entity
 */
class NotificacionLeida
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
     * @ORM\ManyToOne(targetEntity="User",inversedBy="notificaciones")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;
    
    /**
     * @ORM\ManyToOne(targetEntity="Notificacion",inversedBy="leidos")
     * @ORM\JoinColumn(name="notificacion_id", referencedColumnName="id")
     */
    private $notificacion;
    
    /**
    * @ORM\Column(type="datetime")
    */
    private $leido;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->leido = new \DateTime();
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
     * Set user
     *
     * @param \pDev\UserBundle\Entity\User $user
     * @return NotificacionLeida
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
     * Set notificacion
     *
     * @param \pDev\UserBundle\Entity\Notificacion $notificacion
     * @return NotificacionLeida
     */
    public function setNotificacion(\pDev\UserBundle\Entity\Notificacion $notificacion = null)
    {
        $this->notificacion = $notificacion;
    
        return $this;
    }

    /**
     * Get notificacion
     *
     * @return \pDev\UserBundle\Entity\Notificacion 
     */
    public function getNotificacion()
    {
        return $this->notificacion;
    }

    /**
     * Set leido
     *
     * @param \DateTime $leido
     * @return NotificacionLeida
     */
    public function setLeido($leido)
    {
        $this->leido = $leido;
    
        return $this;
    }

    /**
     * Get leido
     *
     * @return \DateTime 
     */
    public function getLeido()
    {
        return $this->leido;
    }
}