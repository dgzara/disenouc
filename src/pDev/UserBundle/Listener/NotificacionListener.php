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
        
        // Obtenemos los funcionarios
        $funcionarios = $this->em->getRepository('pDevUserBundle:Funcionario')->findAll();
        
        if ($entity instanceof \pDev\PracticasBundle\Entity\Practica) 
        {
            $enlace = $this->container->get('router')->generate('practicas_show',array('id'=>$entity->getId()), true);
            $this->armarNotificacion("Oferta de práctica enviada a revisión", "Los datos han sido enviados y serán revisados para su publicación", $user, $enlace);
            $mensaje = "La empresa ".$entity->getOrganizacion()." ha enviado la oferta ".$entity->getNombre();
            
            foreach($funcionarios as $funcionario)
                $this->armarNotificacion("Nueva oferta de práctica", $mensaje, $funcionario->getUsuario(), $enlace);
            
            $this->em->flush();
        }
        elseif($entity instanceof \pDev\PracticasBundle\Entity\AlumnoPracticante) 
        {
            $enlace = $this->container->get('router')->generate('practicas_alumno_show',array('id'=>$entity->getId()), true);
            
            if($entity->getPractica())
            {
                $this->armarNotificacion("Postulación realizada ", "Los datos han sido enviados y serán revisados para su aprobación", $user, $enlace);
                $mensaje = "El alumno ".$entity->getAlumno()." ha postulado a la practica ".$entity->getPractica()->getNombre();
                $this->armarNotificacion("Nueva postulación", $mensaje, $entity->getPractica()->getCreador(), $enlace);      
            }
            else 
            {
                $this->armarNotificacion("Plan de práctica enviado a revisión", "Los datos han sido enviados y serán revisados para su aprobación", $user, $enlace);
                $mensaje = "El alumno ".$entity->getAlumno()." ha enviado el plan de practica en ".$entity->getOrganizacion();
                
                foreach($funcionarios as $funcionario)
                    $this->armarNotificacion("Nuevo plan de práctica", $mensaje, $funcionario->getUsuario(), $enlace); 
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

        // Obtenemos los funcionarios
        $funcionarios = $this->em->getRepository('pDevUserBundle:Funcionario')->findAll();

        foreach ($uow->getScheduledEntityUpdates() AS $entity) 
        {
            if ($entity instanceof \pDev\PracticasBundle\Entity\Practica)
            {
                $enlace = $this->container->get('router')->generate('practicas_show',array('id'=>$entity->getId()), true);
                $changeSet = $uow->getEntityChangeSet($entity);
                $cambio = false;
                $usuarios->add($entity->getCreador());
                
                if(array_key_exists('estado', $changeSet))
                {
                    if($changeSet['estado'][1] == "estado.aprobada")
                    {
                        $titulo = "Oferta publicada";
                        $mensaje = "La oferta '".$entity->getNombre()."' ha sido aprobada, ahora se encuentra publicada en la bolsa de trabajo";
                        $cambio = true;
                    }
                    else if($changeSet['estado'][1] == "estado.rechazada")
                    {
                        $titulo = "Oferta rechazada";
                        $mensaje = "La oferta '".$entity->getNombre()."' ha sido rechazada";
                        $cambio = true;
                    }
                    else if($changeSet['estado'][1] == "estado.pendiente")
                    {
                        $titulo = "Realizar modificaciones a oferta";
                        $mensaje = "La oferta '".$entity->getNombre()."' puede ser publicada bajo ciertas modificaciones";
                        $cambio = true;
                    }
                    else if($changeSet['estado'][1] == "estado.revision")
                    {
                        $titulo = "Oferta enviada a revisión";
                        $mensaje = "La oferta '".$entity->getNombre()."' ha sido enviada a los coordinadores para su revisión";
                        $cambio = true;
                        foreach($funcionarios as $funcionario)
                            $usuarios->add($funcionario->getUsuario());
                    }
                    
                    if($cambio){
                        foreach($usuarios as $usuario)
                            $this->armarNotificacion($titulo, $mensaje, $usuario, $enlace);
                    }
                }
            }
            elseif ($entity instanceof \pDev\PracticasBundle\Entity\AlumnoPracticante)
            {
                $enlace = $this->container->get('router')->generate('practicas_alumno_show',array('id'=>$entity->getId()), true);
                $changeSet = $uow->getEntityChangeSet($entity);
                $cambio = false;
            
                if(array_key_exists('estado', $changeSet))
                {   
                    if($changeSet['estado'][1] == "estado.enviada")
                    {
                        $titulo = "Nuevo plan de práctica";
                        $mensaje = "El alumno ".$entity->getAlumno()." ha enviado un plan de práctica con la organización ".$entity->getOrganizacion();
                        $cambio = true;
                        if($entity->getSupervisor())
                            $usuarios->add($entity->getSupervisor()->getUsuario());
                        foreach($funcionarios as $funcionario)
                            $usuarios->add($funcionario->getUsuario());
                    }
                    else if($changeSet['estado'][1] == "estado.aceptada.contacto")
                    {
                        $titulo = "Postulación aceptada por la organización";
                        $mensaje = "La postulación del alumno ".$entity->getAlumno()." ha sido aceptada por la organización ".$entity->getOrganizacion();
                        $cambio = true;
                        $usuarios->add($entity->getAlumno()->getUsuario());
                        foreach($entity->getContactos() as $contacto)
                            $usuarios->add($contacto->getUsuario());
                        foreach($funcionarios as $funcionario)
                            $usuarios->add($funcionario->getUsuario());
                    }
                    elseif($changeSet['estado'][1] == "estado.pendiente")
                    {
                        $titulo = "Plan de práctica pendiente";
                        $mensaje = "El plan del alumno ".$entity->getAlumno()." se encuentra en proceso de revisión";
                        $cambio = true;
                        $usuarios->add($entity->getAlumno()->getUsuario());
                        foreach($entity->getContactos() as $contacto)
                            $usuarios->add($contacto->getUsuario());
                        if($entity->getSupervisor())
                            $usuarios->add($entity->getSupervisor()->getUsuario());
                    }
                    elseif($changeSet['estado'][1] == "estado.aprobada")
                    {
                        $titulo = "Plan de práctica aprobada por coordinador";
                        $mensaje = "Tu práctica ha sido aprobada por Coordinación, puedes comenzar de acuerdo a la fecha estipulada en tu Plan. Recuerda que debes asistir a la supervisión intermedia con la ficha de supervisión que puedes encontrar en este mismo sitio.";
                        $cambio = true;
                        $usuarios->add($entity->getAlumno()->getUsuario());
                        foreach($entity->getContactos() as $contacto)
                            $usuarios->add($contacto->getUsuario());
                        if($entity->getSupervisor())
                            $usuarios->add($entity->getSupervisor()->getUsuario());
                    }
                    else if($changeSet['estado'][1] == "estado.rechazada")
                    {
                        $titulo = "Plan de práctica rechazada";
                        $mensaje = "El plan del alumno ".$entity->getAlumno()."en la organización ".$entity->getOrganizacion()." ha sido rechazada";
                        $cambio = true;
                        $usuarios->add($entity->getAlumno()->getUsuario());
                        foreach($entity->getContactos() as $contacto)
                            $usuarios->add($contacto->getUsuario());
                        if($entity->getSupervisor())
                            $usuarios->add($entity->getSupervisor()->getUsuario());
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
                    else if($changeSet['estado'][1] == "estado.iniciada")
                    {
                        $titulo = "Plan de práctica iniciado";
                        $mensaje = "El alumno ".$entity->getAlumno()."ha iniciado su práctica en la organización ".$entity->getOrganizacion();
                        $cambio = true;
                        foreach($funcionarios as $funcionario)
                            $usuarios->add($funcionario->getUsuario());
                    }
                    else if($changeSet['estado'][1] == "estado.terminada")
                    {
                        $titulo = "Plan de práctica finalizado";
                        $mensaje = "El alumno ".$entity->getAlumno()." ha finalizado su prácica en la organización ".$entity->getOrganizacion();
                        $cambio = true;
                        foreach($funcionarios as $funcionario)
                            $usuarios->add($funcionario->getUsuario());
                    }
                    else if($changeSet['estado'][1] == "estado.informe")
                    {
                        $titulo = "Informe de Plan de práctica realizado";
                        $mensaje = "El alumno ".$entity->getAlumno()." ha realizado su informe de práctica en la organización ".$entity->getOrganizacion();
                        $cambio = true;
                        foreach($funcionarios as $funcionario)
                            $usuarios->add($funcionario->getUsuario());
                    }
                    else if($changeSet['estado'][1] == "estado.evaluada")
                    {
                        $titulo = "Evaluada el plan de práctica";
                        $mensaje = "El informe de práctica del alumno ".$entity->getAlumno()." ha sido evaluada";
                        $cambio = true;
                        $usuarios->add($entity->getAlumno()->getUsuario());
                    }
                    
                    if($cambio){
                        foreach($usuarios as $usuario)
                            $this->armarNotificacion($titulo, $mensaje, $usuario, $enlace);
                    }
                }
                
                if(array_key_exists('profesor', $changeSet))
                {
                    $titulo = "Asignado profesor para evaluación";
                    $mensaje = "El profesor ".$entity->getProfesor()." ha sido asignado para evaluar la práctica del alumno ".$entity->getAlumno();
                    
                    // Agregamos a los usuarios notificados
                    $usuarios->add($entity->getAlumno()->getUsuario());
                    $usuarios->add($entity->getProfesor()->getUsuario());
                    
                    foreach($usuarios as $usuario)
                        $this->armarNotificacion($titulo, $mensaje, $usuario, $enlace);
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
        $notificacion->setEnlace($enlace);   
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
