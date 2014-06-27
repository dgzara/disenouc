<?php
namespace pDev\UserBundle\DependencyInjection;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use pDev\UserBundle\Entity\Permiso;

class PermissionManager
{
    protected $container;
    protected $em;
    
    public function __construct(EntityManager $em, ContainerInterface $container)
    {
        $this->em = $em;
        $this->container = $container;
    }
    
    /*
     * Chequea permiso
     */
    public function isGranted($role, $modulo, $user_tipo = null)
    {
        $user = $this->getUser();
        $securityContext = $this->container->get('security.context');
        
        // si es super admin
        if (true === $securityContext->isGranted("ROLE_SUPER_ADMIN")) {
            return true;
        }
        
        // si es del tipo y tiene permiso
        if((!$user_tipo or $this->checkType($user_tipo,$user)) and $this->hasPermiso($role,$modulo,$user))
            return true;
        
    	return false;
    }
    
    /*
     * Chequea tipo
     */
    public function checkType($user_tipo,$user=null)
    {
        if(!$user)
            $user = $this->getUser();
               
        if($user and $user_tipo)
        {
            // chequeamos tipo usuario
            if($user->hasPersona($user_tipo))
                return true;
        }
        
    	return false;
    }
    
    public function checkUsername($username,$user=null)
    {
        if(!$user)
            $user = $this->getUser();
               
        if($user and $username)
        {
            // chequeamos nombre usuario
            if($user->getUsername()==$username)
                return true;
        }
        
    	return false;
    }
    
    public function isGrantedForbidden($role, $modulo, $user_tipo = null)
    {
        if(!$this->isGranted($role, $modulo, $user_tipo))
            throw new AccessDeniedException();
    }
    
    public function checkAlumnoRut($rut)
    {
        if($rut and $this->checkType("TYPE_ALUMNO") and $this->getUser()->getPersona('TYPE_ALUMNO')->getRut()==$rut)
            return true;
        return false;
    }
    
    public function throwForbidden($condicion = true)
    {
        if($condicion)
            throw new AccessDeniedException();
    }
    
    public function getUser()
    {
        $user = null;
        $securityContext = $this->container->get('security.context');
        if($securityContext and $securityContext->getToken())
        {
            $user = $securityContext->getToken()->getUser();
        }       
        
        if(!$user or $user === "" or $user == "anon."){
            return null;
            //throw new NotFoundHttpException('Usuario no hallado');
        }
        
        return $user;
    }
    
    //crea los permisos básicos para un usuario
    public function createPermisos($user = null)
    {
        if(!$user)
            $user = $this->getUser();
            
        $em = $this->em;

        //$roles = $em->getRepository('pDevUserBundle:Role')->findAll();
        $basic_role = $em->getRepository('pDevUserBundle:Role')->findOneByOrden(0);
        $sites = $em->getRepository('pDevUserBundle:Sitio')->findAll();
        
        foreach($sites as $site)
        {
            $permiso = $em->getRepository('pDevUserBundle:Permiso')->findOneBy(array('user'=>$user->getId(),'site'=>$site->getId()));
            if(!$permiso)
            {
                $permiso = new Permiso();
                $permiso->setSite($site);
                $permiso->setRole($basic_role);
                $permiso->setUser($user);
                $em->persist($permiso);
            }
        }
        
        $em->flush();
        
    }
    
    public function setPermiso($role, $modulo, $user = null)
    {
        if(!$user)
            $user = $this->getUser();
            
        $em = $this->em;

        //$roles = $em->getRepository('pDevUserBundle:Role')->findAll();
        $role = $em->getRepository('pDevUserBundle:Role')->findOneByRole($role);
        $site = $em->getRepository('pDevUserBundle:Sitio')->findOneBySite($modulo);
        
        if($role and $site and $user)
        {
            $permiso = $em->getRepository('pDevUserBundle:Permiso')->findOneBy(array('user'=>$user->getId(),'site'=>$site->getId()));
            if(!$permiso)
            {
                $permiso = new Permiso();
                $permiso->setSite($site);
                $permiso->setUser($user);
                $em->persist($permiso);
            }
            $permiso->setRole($role);
            
            $em->flush();
            return true;
        }
        return false;
    }
    
    private function hasPermiso($rolestring,$sitestring,$user=null)
    {
        if($user)
        {
            $em = $this->em;
            $role = $em->getRepository('pDevUserBundle:Role')->findOneByRole($rolestring);
            if($role)
            {       
                if($role->getOrden()===0)
                    return true;

                $site = $em->getRepository('pDevUserBundle:Sitio')->findOneBySite($sitestring);
                if($site)
                {
                    $permiso = $em->getRepository('pDevUserBundle:Permiso')->findOneBy(array('user'=>$user->getId(),'site'=>$site->getId()));
                    if($permiso)
                    {
                        if($role->getOrden() <= $permiso->getRole()->getOrden())
                            return true;
                    }
                }
            }
        }
        return false;
    }
}
