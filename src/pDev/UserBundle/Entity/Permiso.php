<?php

namespace pDev\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Foto
 *
 * @ORM\Table(name="nb_user_permiso")
 * @ORM\Entity
 */
class Permiso
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
     * @ORM\ManyToOne(targetEntity="User",inversedBy="permisos",cascade="persist")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;
    
    /**
     * @ORM\ManyToOne(targetEntity="Role",cascade="persist")
     * @ORM\JoinColumn(name="role_id", referencedColumnName="id")
     */
    private $role;
    
    /**
     * @ORM\ManyToOne(targetEntity="Sitio",cascade="persist")
     * @ORM\JoinColumn(name="sitio_id", referencedColumnName="id")
     */
    private $site;

    public function __toString()
    {
        return $this->site->getNombre();
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
     * @return Permiso
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
     * Set role
     *
     * @param \pDev\UserBundle\Entity\Role $role
     * @return Permiso
     */
    public function setRole(\pDev\UserBundle\Entity\Role $role = null)
    {
        $this->role = $role;
    
        return $this;
    }

    /**
     * Get role
     *
     * @return \pDev\UserBundle\Entity\Role 
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set site
     *
     * @param \pDev\UserBundle\Entity\Sitio $site
     * @return Permiso
     */
    public function setSite(\pDev\UserBundle\Entity\Sitio $site = null)
    {
        $this->site = $site;
    
        return $this;
    }

    /**
     * Get site
     *
     * @return \pDev\UserBundle\Entity\Sitio 
     */
    public function getSite()
    {
        return $this->site;
    }
}
