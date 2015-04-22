<?php
namespace pDev\UserBundle\DataFixtures\ORM;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use pDev\PracticasBundle\Entity\AlumnoPracticante;

class LoadAlumnoPracticanteData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface, ContainerAwareInterface
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
		    // Practica finalizada
		    $practicante = new AlumnoPracticante();
		    $practicante->setAlumno($this->getReference('persona-alumno'));
		    $practicante->setSupervisor($this->getReference('persona-supervisor'));
		    $practicante->setOrganizacion($this->getReference('organizacion'));
		    $practicante->setTipo("Oficina");
		    $practicante->setFechaInicio(new \DateTime('2014-05-01'));
		    $practicante->setDuracionCantidad(2);
		    $practicante->setDuracionUnidad("meses");
		    $practicante->setEstado("estado.terminada");
		    $practicante->setComoContacto("Contacto propio");
		    
		    $manager->persist($practicante);
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
