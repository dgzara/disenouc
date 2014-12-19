<?php

namespace pDev\UserBundle\Listener;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use pDev\UserBundle\Entity\Notificacion;
use Doctrine\Common\Collections\ArrayCollection;

class NotificacionListener
{   
    protected $em;
    protected $container;
	
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $this->em = $args->getEntityManager();
        
        if(!$this->container->get('security.context')->getToken())
            return;
            
        $user = $this->container->get('security.context')->getToken()->getUser();
        if(!$this->container->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')){
            return;
        }
        
        if ($entity instanceof \pDev\PracticasBundle\Entity\Practica) 
        {
            $enlace = $this->container->get('router')->generate('practicas_show',array('id'=>$entity->getId()), true);
            $this->armarNotificacion("Oferta de práctica publicada", "Ha sido publicada la oferta", $user, $enlace);
            
            // Obtenemos los funcionarios
            $funcionarios = $this->em->getRepository('pDevUserBundle:Funcionario')->findAll();
            $mensaje = "La empresa ".$entity->getOrganizacionAlias()." ha publicado la oferta ".$entity->getNombre();
            
            foreach($funcionarios as $funcionario)
            {
                $this->armarNotificacion("Nueva oferta de práctica", $mensaje, $funcionario->getUsuario(), $enlace);
            }
            
            $this->em->flush();
        }
    }
    
    public function onFlush(OnFlushEventArgs $eventArgs)
    {
        $this->em = $eventArgs->getEntityManager();
        if(!$this->container->get('security.context')->getToken())
            return;
        
        // Obtenemos a los usuarios
        $user = $this->container->get('security.context')->getToken()->getUser();
        if(!$this->container->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')){
            return;
        }
        
        $uow = $this->em->getUnitOfWork();
        $usuarios = new ArrayCollection();
                
        foreach ($uow->getScheduledEntityUpdates() AS $entity) 
        {
            if ($entity instanceof \pDev\PracticasBundle\Entity\Practica)
            {
                $enlace = $this->container->get('router')->generate('practicas_show',array('id'=>$entity->getId()), true);
                $changeSet = $uow->getEntityChangeSet($entity);
                $cambio = false;
            
                if(array_key_exists('estado', $changeSet))
                {
                    if($changeSet['estado'][1] == "estado.aprobada")
                    {
                        $titulo = "Oferta publicada";
                        $mensaje = "La oferta ha sido aprobada, ahora se encuentra publicada en la bolsa de trabajo";
                        $cambio = true;
                    }
                    else if($changeSet['estado'][1] == "estado.rechazada")
                    {
                        $titulo = "Oferta rechazada";
                        $mensaje = "La oferta ha sido rechazada";
                        $cambio = true;
                    }
                    else if($changeSet['estado'][1] == "estado.pendiente")
                    {
                        $titulo = "Realizar modificaciones a oferta";
                        $mensaje = "La oferta puede ser publicada bajo ciertas modificaciones";
                        $cambio = true;
                    }
                    
                    if($cambio)
                        $this->armarNotificacion($titulo, $mensaje, $entity->getCreador(), $enlace);
                }
            }
            elseif ($entity instanceof \pDev\PracticasBundle\Entity\AlumnoPracticante)
            {
                $enlace = $this->container->get('router')->generate('practicas_alumno_show',array('id'=>$entity->getId()), true);
                $changeSet = $uow->getEntityChangeSet($entity);
                $cambio = false;
            
                if(array_key_exists('estado', $changeSet))
                {   
                    // Obtenemos los funcionarios
                    $funcionarios = $this->em->getRepository('pDevUserBundle:Funcionario')->findAll();
                    
                    if($changeSet['estado'][1] == "estado.enviada")
                    {
                        $titulo = "Nuevo plan de práctica";
                        $mensaje = "";
                        $cambio = true;
                        $usuarios->add($entity->getSupervisor()->getUsuario());
                        foreach($funcionarios as $funcionario)
                            $usuarios->add($funcionario->getUsuario());
                    }
                    else if($changeSet['estado'][1] == "estado.aceptada.contacto")
                    {
                        $titulo = "Plan de práctica aceptada por la empresa";
                        $mensaje = "";
                        $cambio = true;
                        $usuarios->add($entity->getSupervisor()->getUsuario());
                        $usuarios->add($entity->getAlumno()->getUsuario());
                        foreach($funcionarios as $funcionario)
                            $usuarios->add($funcionario->getUsuario());
                    }
                    elseif($changeSet['estado'][1] == "estado.pendiente")
                    {
                        $titulo = "Plan de práctica pendiente";
                        $mensaje = "";
                        $cambio = true;
                        $usuarios->add($entity->getAlumno()->getUsuario());
                        $usuarios->add($entity->getSupervisor()->getUsuario());
                    }
                    elseif($changeSet['estado'][1] == "estado.aprobada")
                    {
                        $titulo = "Plan de práctica aprobada por coordinador";
                        $mensaje = "Plan de práctica aprobada por coordinador";
                        $cambio = true;
                        $usuarios->add($entity->getAlumno()->getUsuario());
                        $usuarios->add($entity->getSupervisor()->getUsuario());
                    }
                    else if($changeSet['estado'][1] == "estado.rechazada")
                    {
                        $titulo = "Plan de práctica rechazada";
                        $mensaje = "";
                        $cambio = true;
                        $usuarios->add($entity->getAlumno()->getUsuario());
                        $usuarios->add($entity->getSupervisor()->getUsuario());
                    }
                    else if($changeSet['estado'][1] == "estado.revision")
                    {
                        $titulo = "Plan de práctica modificada";
                        $mensaje = "El alumno ha realizado modificaciones al plan de practica";
                        $cambio = true;
                        foreach($funcionarios as $funcionario)
                            $usuarios->add($funcionario->getUsuario());
                    }
                    else if($changeSet['estado'][1] == "estado.aceptada.alumno")
                    {
                        $titulo = "Plan de práctica aceptada por el alumno";
                        $mensaje = "";
                        $cambio = true;
                        $usuarios->add($entity->getSupervisor()->getUsuario());
                        foreach($funcionarios as $funcionario)
                            $usuarios->add($funcionario->getUsuario());
                    }
                    else if($changeSet['estado'][1] == "estado.aceptada.supervisor")
                    {
                        $titulo = "Plan de práctica aceptada por el supervisor";
                        $mensaje = "";
                        $cambio = true;
                        $usuarios->add($entity->getAlumno()->getUsuario());
                        foreach($funcionarios as $funcionario)
                            $usuarios->add($funcionario->getUsuario());
                    }
                    else if($changeSet['estado'][1] == "estado.aceptada")
                    {
                        $titulo = "Plan de práctica aceptada";
                        $mensaje = "";
                        $cambio = true;
                        $usuarios->add($entity->getAlumno()->getUsuario());
                        $usuarios->add($entity->getSupervisor()->getUsuario());
                        foreach($funcionarios as $funcionario)
                            $usuarios->add($funcionario->getUsuario());
                    }
                    else if($changeSet['estado'][1] == "estado.iniciada")
                    {
                        $titulo = "Plan de práctica iniciado";
                        $mensaje = "";
                        $cambio = true;
                        foreach($funcionarios as $funcionario)
                            $usuarios->add($funcionario->getUsuario());
                    }
                    else if($changeSet['estado'][1] == "estado.terminada")
                    {
                        $titulo = "Plan de práctica finalizado";
                        $mensaje = "";
                        $cambio = true;
                        foreach($funcionarios as $funcionario)
                            $usuarios->add($funcionario->getUsuario());
                    }
                    else if($changeSet['estado'][1] == "estado.informe")
                    {
                        $titulo = "Informe de Plan de práctica realizado";
                        $mensaje = "";
                        $cambio = true;
                        foreach($funcionarios as $funcionario)
                            $usuarios->add($funcionario->getUsuario());
                    }
                    else if($changeSet['estado'][1] == "estado.evaluada")
                    {
                        $titulo = "Evaluación de Plan de práctica";
                        $mensaje = "";
                        $cambio = true;
                        $usuarios->add($entity->getAlumno()->getUsuario());
                    }
                    
                    if($cambio){
                        foreach($usuarios as $usuario)
                            $this->armarNotificacion($titulo, $mensaje, $usuario, $enlace);
                    }
                }
            }
        }
    }
    
    private function armarNotificacion($titulo, $mensaje, $user, $enlace, $key = Notificacion::USER_NOTICE)
    {
        if(!$user)
        {
            $pm = $this->container->get("permission.manager");
            $user = $pm->getUser();
        }

        $notificacion = new Notificacion();
        $notificacion->setTitulo($titulo);
        $notificacion->setUser($user);
        $notificacion->setMensaje($mensaje);
        $notificacion->setLlave($key);     
        $this->em->persist($notificacion);
        
        if($user->recibeCorreo())
        {
            // Creamos el correo
            $message = \Swift_Message::newInstance()
                ->setSubject($titulo)
                ->setFrom('no-reply@uc.cl')
                ->setTo($user->getEmail())
                ->setBody($this->container->get('templating')->render('pDevUserBundle:Notificacion:email.html.twig', array(
                    'nombre' => $user->__toString(), 
                    'mensaje' => $mensaje, 
                    'direccion' => $enlace,
                    'configuracion' => $this->container->get('router')->generate('fos_user_profile_edit', array(), true)
                )), 'text/html');
            $this->container->get('mailer')->send($message);
        }
        
        // Guardamos los cambios
	    $this->em->persist($notificacion);
	    $metadata = $this->em->getClassMetadata('pDev\UserBundle\Entity\Notificacion');
	    $this->em->getUnitOfWork()->computeChangeSet($metadata, $notificacion);
    }
}
