<?php
namespace pDev\UserBundle\DependencyInjection;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use pDev\UserBundle\Entity\Notificacion;
use pDev\UserBundle\Entity\NotificacionLeida;

class NotificationManager
{
    protected $container;
    protected $em;
    
    
    public function __construct(EntityManager $em, ContainerInterface $container)
    {
        $this->em = $em;
        $this->container = $container;
    }
        
    //crea un registro para el usuario actual
    public function createNotificacion($mensaje,$key = Notificacion::USER_NOTICE,$user = null,$leido = false)
    {
        if($key === Notificacion::USER_ALERT 
                || $key === Notificacion::USER_FORBIDDEN 
                || $key === Notificacion::USER_SUCCESS 
                || $key === Notificacion::USER_ERROR)
        {
            $this->container->get('session')->getFlashBag()->add(
                        $key,
                        $mensaje
                    );
        }
        else
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
            
            $em->flush();
        }
    }
    
    public function createNotificacionLeida($mensaje,$key = Notificacion::USER_NOTICE,$user = null)
    {
        $this->createNotificacion($mensaje,$key,$user,true);
    }
    
    /*
     * Envia Email a usuario
     */
    public function sendMail($address,$subject,$body)
    {
        $ch = $this->container->get("context.helper");        
        $from = $ch->getConfigValue('email_user');        
        //$to = $ch->getConfigValue('email_user');
        $to = $address;
        
        
        
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($from)
            ->setTo($to)
            ->setBody($body);

        // Send the message
        return $this->mailerSend($message);

        // $this->get('mailer')->send($message);
        
    }
    
    /*
     * Envia Email a usuarios
     */
    public function sendMailMasivo(array $addreses_array,$subject,$body)
    {
        $ch = $this->container->get("context.helper");        
        $from = $ch->getConfigValue('email_user');        
        $to = $ch->getConfigValue('email_user');
        //$to = $address;
        
        
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($from)
            ->setTo($to);
        
        $flag = true;
        $bcc_limit = 5;
        
        while(count($addreses_array)>0 and $flag)
        {
            $offset = count($addreses_array)>$bcc_limit?count($addreses_array)-$bcc_limit:0;
            $length = count($addreses_array)>$bcc_limit?$bcc_limit:count($addreses_array);
            
            $aslice = array_slice($addreses_array, $offset,$length);
            
            $message = $message->setBcc($aslice);
        
            $message = $message->setBody($body);

            $flag = $this->mailerSend($message);
            
            $addreses_array = array_slice($addreses_array, 0, count($addreses_array)-$length);
        }
            
        return $flag;

        // $this->get('mailer')->send($message);
        
    }
    
    private function mailerSend($message)
    {
        $ch = $this->container->get("context.helper");
        $from = $ch->getConfigValue('email_user');
        $em = $this->em;
        
        $nombre = null;
        $user = $em->getRepository('pDevUserBundle:User')->findOneByEmail($from);
        if($user)
        {
            $nombre = $user->getNombreCompleto();
        }
        
        // Create the Transport
        $transport = \Swift_SmtpTransport::newInstance('smtp-externo.puc.cl', 25)
          ->setUsername($from)
          ->setPassword($ch->getConfigValue('email_pass'))
          ;

        // Create the Mailer using your created Transport
        $mailer = \Swift_Mailer::newInstance($transport);

        $message = $message->setFrom($from,$nombre);
                
        $sent = true;
        
        try {
            $mailer->send($message);
        } catch (\Exception $e) {
            // error de autentificación
            $sent = false;
        }
        
        // Send the message
        return $sent;

        // $this->get('mailer')->send($message);
        
    }
}
