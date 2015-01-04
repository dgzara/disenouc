<?php
namespace pDev\UserBundle\DataFixtures\ORM;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use pDev\PracticasBundle\Entity\Practica;

class LoadPracticaData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface, ContainerAwareInterface
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
		    // Practica ofrecida
		    $practica = new Practica();
		    $practica->setNombre("Videos 3d");
		    $practica->setDescripcion("Establecimiento de gráfica");
		    $practica->setContacto($this->getReference('persona-contacto'));
		    $practica->setCreador($this->getReference('user-contacto'));
		    $practica->setSupervisor($this->getReference('persona-supervisor'));
		    $practica->setOrganizacionAlias($this->getReference('organizacion-alias'));
		    $practica->setJornadas("Full-time");
		    $practica->setFechaInicio(new \DateTime('2014-05-01'));
		    $practica->setDuracionCantidad(2);
		    $practica->setDuracionUnidad("meses");
		    $practica->setEstado("estado.revision");
		    $manager->persist($practica);
		    
		    // Practica publicada
		    $practica = new Practica();
		    $practica->setNombre("Diseño de imagen corporativa");
		    $practica->setDescripcion("Establecimiento de gráfica");
		    $practica->setContacto($this->getReference('persona-contacto'));
		    $practica->setCreador($this->getReference('user-contacto'));
		    $practica->setTipo("Oficina");
		    $practica->setSupervisor($this->getReference('persona-supervisor'));
		    $practica->setOrganizacionAlias($this->getReference('organizacion-alias'));
		    $practica->setJornadas("Full-time");
		    $practica->setFechaInicio(new \DateTime('2014-05-01'));
		    $practica->setDuracionCantidad(2);
		    $practica->setDuracionUnidad("meses");
		    $practica->setEstado("estado.aprobada");
		    $manager->persist($practica);
		    
		    $manager->flush();
		}
	}
	
	/**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 4; // the order in which fixtures will be loaded
    }
}
