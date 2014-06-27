<?php
namespace pDev\UserBundle\DependencyInjection;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use pDev\UserBundle\Entity\Registro;

class LoggerManager
{
    protected $container;
    protected $em;
    
    public function __construct(EntityManager $em, ContainerInterface $container)
    {
        $this->em = $em;
        $this->container = $container;
    }
        
    //crea un registro para el usuario 
    public function createRegistro($mensaje,$user = null)
    {
        if(!$user)
        {
            $securityContext = $this->container->get('security.context');
            $user = $securityContext->getToken()->getUser();

            if(!$user or $user === "" or $user == "anon."){
                $user = null;
            }
        }
        
        $em = $this->em;
        
        $log = new Registro();
        $log->setUser($user);
        $log->setMensaje($mensaje);
        $em->persist($log);
        $em->flush();        
    }    
}
