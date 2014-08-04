<?php
namespace pDev\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use pDev\UserBundle\Entity\Archivo;

class LoadPlantillasData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $owner = $this->getReference('user_default');
        
        $plantilla2 = new Archivo();
        $plantilla2->setMimetype('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $plantilla2->setPath('plantilla_personas.xlsx');
        $sitio2 = $this->getReference('SITE_PERSONAS');
        $plantilla2->setSite($sitio2);        
        $plantilla2->setOwner($owner);
        $plantilla2->setType('.xlsx');
        $manager->persist($plantilla2);
        
        $manager->flush();
    }
    
    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 4; // the order in which fixtures will be loaded
    }
}
