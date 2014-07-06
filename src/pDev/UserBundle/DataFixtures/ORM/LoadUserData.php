<?php
namespace pDev\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use pDev\UserBundle\Entity\User;
use pDev\UserBundle\Entity\Funcionario;
use pDev\UserBundle\Entity\Permiso;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $userpersona = $manager->getRepository('pDevUserBundle:Funcionario')->findOneByRut("166284740");
        if(!$userpersona)
            $userPersona = new Funcionario();
        $userPersona->setNombres("Diego Alonso");
        $userPersona->setApellidoPaterno("Gómez");
        $userPersona->setApellidoMaterno("Zará");
        $userPersona->setEmail("dlgomez@uc.cl");
        $userPersona->setRut("166284740");
        
        $manager->persist($userPersona);
        
        $userAdmin = $manager->getRepository('pDevUserBundle:User')->findOneByUsername("dlgomez@uc.cl");
        if(!$userAdmin)
            $userAdmin = new User();
        $userAdmin->setUsername('dlgomez@uc.cl');
        $userAdmin->setPlainPassword('holaFaco');
        $userAdmin->setEmail('dlgomez@uc.cl');
        $userAdmin->setEnabled(true);
        $userAdmin->addPersona($userPersona);
        $userAdmin->setNombres("Diego Alonso");
        $userAdmin->setApellidoPaterno("Gómez");
        $userAdmin->setApellidoMaterno("Zará");
        $userAdmin->setRut("166284740");
        $userPersona->setUsuario($userAdmin);
        $userAdmin->addRole('ROLE_SUPER_ADMIN');
        
        $this->createPermisos($userAdmin,$manager);
        
        $manager->persist($userAdmin);
        
        $this->addReference('user_default', $userAdmin);
        
        $manager->flush();
        
        
    }
    
    private function createPermisos($user = null,$em)
    {
        if(!$user)
            $user = $this->getUser();
            
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
    }
    
    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 2; // the order in which fixtures will be loaded
    }
}
