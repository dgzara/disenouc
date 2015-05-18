<?php
namespace pDev\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use pDev\UserBundle\Entity\Contacto;

class LoadContactoData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $contacto = new Contacto();
        $contacto->setText('<center>
<table style="text-align:center">
    <tbody><tr>
        <td><img src="/recursos/camila-rios-220x220.jpg"></td>
        <td style="width:20px"></td>
        <td><img src="/recursos/domper-220x220.jpg"></td>
    </tr>
    <tr>
        <td><strong>Camila Ríos</strong></td>
        <td></td>
        <td><strong>María Rosa Domper</strong></td>
    </tr>
    <tr>
        <td colspan="3">practicasdiseno @uc.cl</td>
    </tr>
</tbody></table>
</center>');
        
        $manager->persist($contacto);        
        $manager->flush();
    }
    
    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 3; // the order in which fixtures will be loaded
    }
}
