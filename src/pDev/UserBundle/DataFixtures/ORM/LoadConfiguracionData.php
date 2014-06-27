<?php
namespace pDev\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use pDev\UserBundle\Entity\Configuracion;

class LoadConfiguracionData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $categorias = array();
        
        $categorias[] = array(
            'sitio' => 'SITE_AJUSTES',
            'configuraciones' => array(
                array(  'keyName' => 'email_user',
                        'nombre'  => 'Dirección de correo',
                        'descripcion' => 'Correo electrónico para el envío de correos electrónicos (correo UC)',
                        'valor' => 'plreyes@uc.cl',
                        'valorDefault' => '',
                        'valorTipo' => 'string'
                        ),
                array(  'keyName' => 'email_pass',
                        'nombre'  => 'Contraseña',
                        'descripcion' => 'Contraseña para el envío de correos electrónicos',
                        'valor' => 'Wj47pwUC',
                        'valorDefault' => '',
                        'valorTipo' => 'password'
                        )
        ));
                                        
        foreach($categorias as $categoria)
        {
            $sitio = $manager->getRepository('pDevUserBundle:Sitio')->findOneBySite($categoria['sitio']);
            if(!$sitio)
            {
                $sitio = $this->getReference($categoria['sitio']);
            }
            
            foreach($categoria['configuraciones'] as $configuracion)
            {
                $configuracionA1 = $manager->getRepository('pDevUserBundle:Configuracion')->findOneByKeyName($configuracion['keyName']);
                
                if(!$configuracionA1)
                    $configuracionA1 = new Configuracion();
                
                $configuracionA1->setKeyName($configuracion['keyName']);
                $configuracionA1->setNombre($configuracion['nombre']);
                $configuracionA1->setDescripcion($configuracion['descripcion']);
                $configuracionA1->setValor($configuracion['valor']);
                $configuracionA1->setValorDefault($configuracion['valorDefault']);
                $configuracionA1->setValorTipo($configuracion['valorTipo']);
                $configuracionA1->setSitio($sitio);
                $manager->persist($configuracionA1);
            }
        }
        
        
        /*
        
        $categoriaA = new ConfiguracionCategoria();
        $categoriaA->setNombre("");
        $manager->persist($categoriaA);

        $configuracionA1 = new Configuracion();
        $configuracionA1->setKeyName("");
        $configuracionA1->setNombre("");
        $configuracionA1->setDescripcion("");
        $configuracionA1->setValor("");
        $configuracionA1->setValorDefault("");
        $configuracionA1->setCategoria($categoriaA);
        $manager->persist($configuracionA1);
        
        */
                
        
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
