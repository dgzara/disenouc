<?php

namespace pDev\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use pDev\UserBundle\Entity\Notificacion;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="default")
     * @Template()
     */
    public function indexAction()
    {
        if($this->get("permission.manager")->getUser())
            return $this->redirect($this->generateUrl('default_inicio'));
        
        return array();
    }
    
    /**
     * @Route("/acerca", name="about")
     * @Template()
     */
    public function aboutAction()
    {
        $em = $this->getDoctrine()->getManager();
        $documentos = $em->getRepository('pDevUserBundle:Documento')->findAll();
        
        return array('documentos' => $documentos);
    }
    
    /**
     * @Route("/faq", name="preguntas_frecuentes")
     * @Template()
     */
    public function preguntasFrecuentesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $preguntas = $em->getRepository('pDevUserBundle:PreguntaFrecuente')->findAll();
        
        return array('preguntas' => $preguntas);
    }
    
    /**
     * @Route("/contacto", name="contacto")
     * @Template()
     */
    public function contactoAction()
    {
        return array();
    }
    
    /**
     * @Route("/inicio/", name="default_inicio")
     * @Template()
     */
    public function inicioAction()
    {
        // verificamos si el usuario tiene personas asociadas en el sistema
        $user = $this->get('security.context')->getToken()->getUser();
        
        $rut = $user->getRut();
        
        if(!$rut)
            return $this->redirect ($this->generateUrl ('user_edit_basic',array('username'=>$user->getUsername())));
        
        $em = $this->getDoctrine()->getManager();
        
        foreach($user->getPersonas() as $user_persona)
        {
            if($user_persona->getRut()!==$rut)
            {
                $user_persona->setUsuario(null);
                $em->persist($user_persona);
            }
        }
        $em->flush();
        
        $personas = $em->getRepository('pDevUserBundle:Persona')->findByRut($rut);
        
        // si no redirigimos
        if(!$personas or count($personas)===0)
        {
            //return $this->redirect($this->generateUrl('default_externo'));
        }
        
        //si tiene personas asociadas, las asociamos internamente y creamos los permisos
        foreach($personas as $persona)
        {
            if(!$user->getPersonas()->contains($persona))
            {
                if($user->getExternal() and $persona->getTipo() !== 'TYPE_PRACTICAS_CONTACTO' and $persona->getTipo() !== 'TYPE_PRACTICAS_SUPERVISOR')
                    continue;
                
                $user->addPersona($persona);
                $persona->setUsuario($user);
                $em->persist($persona);
            }
        }
                
        $em->persist($user);
        $em->flush();
        
        $this->get("permission.manager")->createPermisos($user);
        //fin
        
        //creamos notificacion de inicio de sesion
        $actualLogin = $user->getLastLogin()?$user->getLastLogin()->format("d-m-Y H:i:s"):'0';
        $previousLogin = $user->getPreviousLogin()?$user->getPreviousLogin()->format("d-m-Y H:i:s"):'0';
        
        
        if($actualLogin !== $previousLogin)
        {
            $clientip = $this->getRequest()->getClientIp();
            $user->setPreviousLogin($user->getLastLogin());
            $em->flush();
            $this->get('logger.manager')->createRegistro('Ha iniciado sesión desde '.$clientip);            
        }
        
        // buscamos actualizaciones que no han sido leidas y que han sido creadas despues del usuario
        $qb = $em->getRepository('pDevUserBundle:Notificacion')->createQueryBuilder('u');
        $qb = $qb->leftJoin('u.leidos','l')
                ->where('(u.user = :userid or u.llave LIKE \'%broadcast%\') AND l IS NULL')
                ->andWhere('u.created > :usercreated')
                ->orderBy('u.created','DESC')
                ;
        $actualizaciones = $qb->setParameter('userid', $user->getId())
                    ->setParameter('usercreated', $user->getCreated())
                    ->getQuery()
                    ->getResult();
        
        return array(
            
            'actualizaciones' =>$actualizaciones
        );
    }
    
    /**
     * @Route("/externo", name="default_externo")
     * @Template()
     */
    public function externoAction()
    {
        $mensaje = 'Sin embargo no se encuentra autorizado a ingresar a esta plataforma, esto puede ocurrir porque no está registrado como usuario perteneciente a la Facultad de Comunicaciones.';
        return array('titulo'=>'Ha sido autentificado correctamente',
            'mensaje'=>$mensaje);
    } 
    
    /**
     * @Route("/login_failure", name="default_login_failure")
     * @Template("pDevWebBundle:Default:externo.html.twig")
     */
    public function loginFailureAction()
    {
        return array('titulo'=>'Hubo un problema con el sistema externo de autentificación',
            'mensaje'=>'Por favor, intente más tarde');
    } 
    
    /**
     * @Route("/demo/", name="default_demo")
     * @Template()
     */
    public function demoAction()
    {
        return array();
    } 
    
    /**
     * @Route("/test", name="default_test")
     * @Template()
     */
    public function testAction()
    {
        /*$ah = $this->get("alumnos.helper");
                
        $taaprobados = $ah->getTestActualidadAprobados(13640844,2014,1);
        print_r('aprobados:'.$taaprobados);

        $tareprobados = $ah->getTestActualidadReprobados(13640844,2014,1);
        print_r('reprobados:'.$tareprobados);
        
        $puedepostular = $ah->getPuedePostularAyudantia(189325223);
        print_r('puede:'.$puedepostular);
         * */
         
        $nm = $this->get("notification.manager");
        $nm->sendMail('pedroare@gmail.com','correo de prueba','test envío individual');
        $varr = $nm->sendMailMasivo(array('pedroare@gmail.com','pareyese@gmail.com'),'correo masivo de prueba','test envío masivo');
        
        
        print_r($varr);
        exit;
    } 
    
    /**
     * @Route("/img/{filename}", name="default_image")
     * @Template()
     */
    public function imgAction($filename)
    {
        return $this->redirect("/img/".$filename,301);
    } 
    
    
}
