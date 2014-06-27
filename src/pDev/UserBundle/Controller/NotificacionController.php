<?php

namespace pDev\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use pDev\UserBundle\Entity\NotificacionLeida;


/**
 * Notificacion controller.
 *
 * @Route("/notificaciones")
 */
class NotificacionController extends Controller
{

    /**
     * Lists all Notificacion entities.
     *
     * @Route("/", name="notificaciones")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $pm = $this->get("permission.manager");
        $em = $this->getDoctrine()->getManager();
        
        //$ch = $this->get("context.helper");
        //$this->notificaMailAction($pm->getUser(),"subj",$ch->getConfigValue('ayudantias_email_rechazar_profesor'));
                
        $leidas = $em->getRepository('pDevUserBundle:Notificacion')->findBy(array('user'=>$pm->getUser()->getId(),'leido'=>true));
        $sinleer = $em->getRepository('pDevUserBundle:Notificacion')->findBy(array('user'=>$pm->getUser()->getId(),'leido'=>false));

        return array(
            'leidas' => array_slice($leidas, 0, 20),
            'noleidas' => array_slice($sinleer, 0, 20),
        );
    }
    
    /**
     * Lists all Notificacion entities.
     *
     * @Template()
     */
    public function notificacionesAction()
    {
        $pm = $this->get("permission.manager");
        $user = $pm->getUser();
        
        $notificaciones = array();
        if($user)
        {
        
            $em = $this->getDoctrine()->getManager();

            $qb = $em->getRepository('pDevUserBundle:Notificacion')->createQueryBuilder('u');
            $qb = $qb->leftJoin('u.leidos','l')
                    ->where('(u.user = :userid or u.llave LIKE \'%broadcast%\') AND l IS NULL')
                    ->andWhere('u.created > :usercreated')
                    ->orderBy('u.created','DESC')
                    ;
            $notificaciones = $qb->setParameter('userid', $user->getId())
                        ->setParameter('usercreated', $user->getCreated())
                        
                        ->setMaxResults( 3 )
                        ->getQuery()
                        ->getResult();

            
        }
        
        return array(
                'notificaciones' => $notificaciones,
            );
    }
    
    /**
     * Lists all Notificacion entities.
     * @Route("/feed/{pagina}", name="notificaciones_feed")
     * @Method("GET")
     * @Template()
     */
    public function notificacionesFeedAction($pagina)
    {
        $pm = $this->get("permission.manager");
        $user = $pm->getUser();
        $notificaciones = array();
        $page = intval($pagina)<1?1:intval($pagina);
        $limit = 10;
        if($user)
        {
            
            $offset = ($page - 1) * $limit;
            $em = $this->getDoctrine()->getManager();

            
            
            $qb = $em->getRepository('pDevUserBundle:Notificacion')->createQueryBuilder('u');
            $qb = $qb->leftJoin('u.leidos','l')
                    ->where('(u.user = :userid or u.llave LIKE \'%broadcast%\') AND l IS NOT NULL')
                    ->andWhere('u.created > :usercreated')
                    ->orderBy('u.created','DESC')
                    ;
            $notificaciones = $qb->setParameter('userid', $user->getId())
                        ->setParameter('usercreated', $user->getCreated())
                        ->setFirstResult( $offset )
                        ->setMaxResults( $limit )
                        ->getQuery()
                        ->getResult();
            
            
        }
        
        return array(
                'notificaciones' => $notificaciones,
                'pagina'=>count($notificaciones)>0?$page+1:-1
            );
    }
    
    
    /**
     * Edits an existing Notificacion entity.
     *
     * @Route("/marcar/{id}", name="notificaciones_leido_ajax")
     */
    public function leidoAjaxAction(Request $request, $id)
    {
        $pm = $this->get("permission.manager");
        $em = $this->getDoctrine()->getManager();
        
        $notificacion = $em->getRepository('pDevUserBundle:Notificacion')->find($id);

        if (!$notificacion) {
            throw $this->createNotFoundException('Unable to find Notificacion entity.');
        }
        $user = $pm->getUser();
        
        $pm->throwForbidden($pm->getUser()->getId() != $notificacion->getUser()->getId());
        
        $leido = $em->getRepository('pDevUserBundle:NotificacionLeida')->findBy(array('user'=>$user->getId(),'notificacion'=>$notificacion->getId()));

        if (!$leido) {
            $leido = new NotificacionLeida();
            $leido->setUser($user);
            $leido->setNotificacion($notificacion);
            $em->persist($leido);
            $em->flush();
        }
        
        $response = new Response(json_encode(array('status' => 'ok')));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
        
    }
}
