<?php
namespace pDev\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use pDev\UserBundle\Entity\Documento;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class LoadDocumentosData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        // Copiamos el archivo del logotipo
        copy(realpath(dirname(__FILE__))."/../../../../../web/recursos/perfil.pdf", realpath(dirname(__FILE__))."/real1.pdf");
        copy(realpath(dirname(__FILE__))."/../../../../../web/recursos/pauta_informe.pdf", realpath(dirname(__FILE__))."/real2.pdf");
        copy(realpath(dirname(__FILE__))."/../../../../../web/recursos/bitacora.pdf", realpath(dirname(__FILE__))."/real3.pdf");
        copy(realpath(dirname(__FILE__))."/../../../../../web/recursos/carta_estudiante.pdf", realpath(dirname(__FILE__))."/real4.pdf");
        copy(realpath(dirname(__FILE__))."/../../../../../web/recursos/carta_supervisor.pdf", realpath(dirname(__FILE__))."/real5.pdf");
        copy(realpath(dirname(__FILE__))."/../../../../../web/recursos/seguro.pdf", realpath(dirname(__FILE__))."/real6.pdf");
        copy(realpath(dirname(__FILE__))."/../../../../../web/recursos/disenouc.zip", realpath(dirname(__FILE__))."/real7.zip");
        copy(realpath(dirname(__FILE__))."/../../../../../web/recursos/uc.zip", realpath(dirname(__FILE__))."/real8.zip");
        
        $real1 = new UploadedFile(realpath(dirname(__FILE__))."/real1.pdf", "real1.pdf", null, null, null, true);
        $real2 = new UploadedFile(realpath(dirname(__FILE__))."/real2.pdf", "real2.pdf", null, null, null, true);
        $real3 = new UploadedFile(realpath(dirname(__FILE__))."/real3.pdf", "real3.pdf", null, null, null, true);
        $real4 = new UploadedFile(realpath(dirname(__FILE__))."/real4.pdf", "real4.pdf", null, null, null, true);
        $real5 = new UploadedFile(realpath(dirname(__FILE__))."/real5.pdf", "real5.pdf", null, null, null, true);
        $real6 = new UploadedFile(realpath(dirname(__FILE__))."/real6.pdf", "real6.pdf", null, null, null, true);
        $real7 = new UploadedFile(realpath(dirname(__FILE__))."/real7.zip", "real7.zip", null, null, null, true);
        $real8 = new UploadedFile(realpath(dirname(__FILE__))."/real8.zip", "real8.zip", null, null, null, true);
        
        /*
        $documento0 = new Documento();
        $documento0->setNombre('Reglamento de practica');
        $documento0->setFile($real0);
        $documento0->setOwner($this->getReference('user_default'));
        $manager->persist($documento0);*/
         
        $documento1 = new Documento();
        $documento1->setNombre('Perfil de Prácticas');
        $documento1->setFile($real1);
        $documento1->setOwner($this->getReference('user_default'));
        $manager->persist($documento1);
        
        $documento2 = new Documento();
        $documento2->setNombre('Pauta de Informe');
        $documento2->setFile($real2);
        $documento2->setOwner($this->getReference('user_default'));
        $manager->persist($documento2);
        
        $documento3 = new Documento();
        $documento3->setNombre('Bitácora');
        $documento3->setFile($real3);
        $documento3->setOwner($this->getReference('user_default'));
        $manager->persist($documento3);
        
        $documento4 = new Documento();
        $documento4->setNombre('Carta de Compromiso estudiante');
        $documento4->setFile($real4);
        $documento4->setOwner($this->getReference('user_default'));
        $manager->persist($documento4);
        
        $documento5 = new Documento();
        $documento5->setNombre('Carta de Compromiso supervisor');
        $documento5->setFile($real5);
        $documento5->setOwner($this->getReference('user_default'));
        $manager->persist($documento5);
        
        $documento6 = new Documento();
        $documento6->setNombre('Certificado Seguro Escolar');
        $documento6->setFile($real6);
        $documento6->setOwner($this->getReference('user_default'));
        $manager->persist($documento6);
        
        $documento7 = new Documento();
        $documento7->setNombre('Logos Diseño');
        $documento7->setFile($real7);
        $documento7->setOwner($this->getReference('user_default'));
        $manager->persist($documento7);
        
        $documento8 = new Documento();
        $documento8->setNombre('Logos UC');
        $documento8->setFile($real8);
        $documento8->setOwner($this->getReference('user_default'));
        $manager->persist($documento8);
        
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
