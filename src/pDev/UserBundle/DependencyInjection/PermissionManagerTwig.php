<?php
namespace pDev\UserBundle\DependencyInjection;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use pDev\UserBundle\DependencyInjection\PermissionManager;

class PermissionManagerTwig extends \Twig_Extension
{
    protected $service;
    
    public function __construct(PermissionManager $service)
    {
        $this->service = $service;
    }
    
    public function getFunctions()
    {
        return array(
            'isTipo' => new \Twig_Function_Method($this, 'checkType'),
        );
    }
    
    public function checkType($userTipo, $user = null)
    {
        return $this->service->checkType($userTipo, $user);
    }
    
    public function getName()
    {
        return 'permission_manager';
    }
}

