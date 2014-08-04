<?php
namespace pDev\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use pDev\UserBundle\Entity\Role;
use pDev\UserBundle\Entity\Sitio;

class LoadPermisoData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $sitios = array();
	    $sitios[] = array('SITE_PRACTICAS','Prácticas');        
        $sitios[] = array('SITE_ALUMNOS','Alumnos');
        $sitios[] = array('SITE_PERSONAS','Personas');        
        $sitios[] = array('SITE_AJUSTES','Ajustes');
        $sitios[] = array('SITE_PERMISOS','Permisos');
        
        foreach($sitios as $sitio_array)
        {
            $sitio = $manager->getRepository('pDevUserBundle:Sitio')->findOneBySite($sitio_array[0]);
            if(!$sitio)
            {
                $sitio = new Sitio();
            }
            $sitio->setSite($sitio_array[0]);
	    $sitio->setNombre($sitio_array[1]);
            $manager->persist($sitio);
            $this->addReference($sitio->getSite(), $sitio);
        }
        
        
        // NIVEL:ROLE nivel es para poder definir herencia
        $roles = array('0:ROLE_USER:Acceso Restringido',
                        '1:ROLE_SUPER_USER:Acceso Privilegiado',
                        '2:ROLE_ADMIN:Acceso Total'
                        );
                        
        foreach($roles as $role)
        {
            $parr= explode(':',$role);
            
            $p = $manager->getRepository('pDevUserBundle:Role')->findOneByRole($parr[1]);
            
            if(!$p)
                $p = new Role();
            
            $p->setRole($parr[1]);
            $p->setOrden($parr[0]);
	    $p->setNombre($parr[2]);
            $manager->persist($p);
        }
        
        $manager->flush();
    }
    
    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1; // the order in which fixtures will be loaded
    }
}
