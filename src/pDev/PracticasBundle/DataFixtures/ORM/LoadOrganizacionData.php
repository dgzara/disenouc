<?php
namespace pDev\UserBundle\DataFixtures\ORM;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\File;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use pDev\PracticasBundle\Entity\Organizacion;
use pDev\PracticasBundle\Entity\OrganizacionAlias;

class LoadOrganizacionData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;

	public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
    
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        if($this->container->getParameter('fixtures') == 'si')
		{
		    // Copiamos el archivo del logotipo
            copy(realpath(dirname(__FILE__))."/logo.jpg", realpath(dirname(__FILE__))."/logo2.jpg");
            $logo = new File(realpath(dirname(__FILE__))."/logo2.jpg", "logo.jpg");
            
		    // Organizacion 
		    $organizacion = new Organizacion();
		    $organizacion->setRubro("Diseño industrial");
		    $organizacion->setDescripcion("Oficina que ofrece nuevos enfoques");
		    $organizacion->setRut("814340419");
		    $organizacion->setPais("Chile");
		    $organizacion->setAntiguedad(43);
		    $organizacion->setWeb("http://www.designcorp.cl");
		    $organizacion->setCreador($this->getReference('user-contacto'));
		    $organizacion->setPersonasTotal(210);
		    $organizacion->addContacto($this->getReference('persona-contacto'));
		    $organizacion->addSupervisor($this->getReference('persona-supervisor'));
		    $organizacion->setProfilePic($logo);
            
            $manager->persist($organizacion);
            $manager->flush();
		    $this->addReference('organizacion', $organizacion);
		    
		    // OrganizacionAlias
		    $organizacionAlias = new OrganizacionAlias();
		    $organizacionAlias->setNombre("DesignCorp");
		    $organizacionAlias->setOrganizacion($this->getReference('organizacion'));
		    
		    $manager->persist($organizacionAlias);
		    $this->addReference('organizacion-alias', $organizacionAlias);
		    $manager->flush();
		}
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 3; // the order in which fixtures will be loaded
    }
}
