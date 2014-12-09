<?php

namespace pDev\UserBundle\Listener;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use pDev\UserBundle\Entity\Notificacion;
use pDev\PracticaBundle\Entity\Practica;

class SearchIndexer
{   
    protected $container;
	
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $entityManager = $args->getEntityManager();

        if ($entity instanceof Practica) {
            // ... do something with the Product
        }
    }
    
    private function armarNotificacion($mensaje, $key = Notificacion::USER_NOTICE, $user)
    {
        $em = $this->em;
            
        if(!$user)
        {
            $pm = $this->container->get("permission.manager");
            $user = $pm->getUser();
        }

        $notificacion = new Notificacion();
        $notificacion->setUser($user);
        $notificacion->setMensaje($mensaje);
        $notificacion->setLlave($key);     
        $em->persist($notificacion);
        
        if($leido)
        {
            $notificacionLeida = new NotificacionLeida();
            $notificacionLeida->setNotificacion($notificacion);
            $notificacionLeida->setUser($user);
            $em->persist($notificacionLeida);
        }
        
        if($user->recibirCorreos())
        {
            // Creamos el correo
            $message = \Swift_Message::newInstance()
                ->setSubject($titulo.$notificacion->getTitulo())
                ->setFrom($this->container->getParameter('comunica_user.from_email'))
                ->setTo($destino->getEmail())
                ->setBody($this->container->get('templating')->render('ComunicaUserBundle:Escritorio:email.html.twig', array(
                    'nombre' => $destino->__toString(), 
                    'mensaje' => $mensaje, 
                    'direccion' => $notificacion->getEnlace(),
                    'configuracion' => $this->container->get('router')->generate('notificacion_configuracion_edit', array(), true)
                )), 'text/html');
            $this->container->get('mailer')->send($message);
        }
        
        // Guardamos los cambios
	    $this->em->persist($notificacion);
	    $this->em->persist($destino);
	    $metadata = $this->em->getClassMetadata('pDev\UserBundle\Entity\Notificacion');
	    $this->em->getUnitOfWork()->computeChangeSet($metadata, $notificacion);
	    $metadata2 = $this->em->getClassMetadata('pDev\UserBundle\Entity\User');
	    $this->em->getUnitOfWork()->computeChangeSet($metadata2, $user);
    }
}
