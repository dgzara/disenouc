<?php

namespace pDev\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use pDev\UserBundle\Entity\Configuracion;
use pDev\UserBundle\Form\ConfiguracionNumeroType;
use pDev\UserBundle\Form\ConfiguracionFechaType;
use pDev\UserBundle\Form\ConfiguracionType;
use pDev\UserBundle\Form\ConfiguracionPasswordType;
use pDev\UserBundle\Form\ConfiguracionStringType;
use pDev\UserBundle\Entity\Notificacion;

/**
 * Configuracion controller.
 *
 * @Route("/ajustes")
 */
class ConfiguracionController extends Controller
{

    /**
     * Lists all Configuracion entities.
     *
     * @Route("/{sitio}", name="configuracion")
     * @Method("GET")
     * @Template()
     */
    public function indexAction($sitio = null)
    {
        $default = "SITE_AJUSTES";
        
        $pm = $this->get("permission.manager");       
        $pm->isGrantedForbidden('ROLE_SUPER_USER',$default);
        
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('pDevUserBundle:Sitio');
        
        if(!$sitio)
            $entities = $entities->findAll();
        else
        {
            $ch = $this->get("context.helper");  
            $sitio = 'SITE_'.$ch->upperizeText($sitio);
            
            $entities = $entities->findBySite($sitio);
        }
        if(!$entities)
        {
            throw $this->createNotFoundException('Unable to find Sitio: '.$sitio.' entity.');
        }
        
        $categorias = array();
        foreach($entities as $sitio)
        {
            if($pm->isGranted('ROLE_SUPER_USER',$sitio->getSite()) and count($sitio->getConfiguraciones())>0)
            {
                $categorias[] = $sitio;
            }            
        }

        return array(
            'categorias' => $categorias,
        );
    }

    /**
     * Displays a form to edit an existing Configuracion entity.
     *
     * @Route("/{id}/edit", name="configuracion_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $pm = $this->get("permission.manager");
        $pm->isGrantedForbidden('ROLE_ADMIN',"SITE_AJUSTES");
        
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('pDevUserBundle:Configuracion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Configuracion entity.');
        }
        
        $pm->isGrantedForbidden('ROLE_ADMIN',$entity->getSitio()->getSite());
        
        $editForm = null;
        if($entity->getValorTipo()=='datetime')
            $editForm = $this->createForm(new ConfiguracionFechaType(), $entity);
        elseif($entity->getValorTipo()=='integer')
            $editForm = $this->createForm(new ConfiguracionNumeroType(), $entity);
        elseif($entity->getValorTipo()=='password')
            $editForm = $this->createForm(new ConfiguracionPasswordType(), $entity);
        elseif($entity->getValorTipo()=='string')
            $editForm = $this->createForm(new ConfiguracionStringType(), $entity);
        else
            $editForm = $this->createForm(new ConfiguracionType(), $entity);
        
        return array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        );
    }

    /**
     * Edits an existing Configuracion entity.
     *
     * @Route("/{id}/update", name="configuracion_update")
     * @Method("PUT")
     * @Template("pDevUserBundle:Configuracion:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $pm = $this->get("permission.manager");
        $pm->isGrantedForbidden('ROLE_ADMIN',"SITE_AJUSTES");
        
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('pDevUserBundle:Configuracion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Configuracion entity.');
        }
        
        $pm->isGrantedForbidden('ROLE_ADMIN',$entity->getSitio()->getSite());

        $editForm = null;
        if($entity->getValorTipo()=='datetime')
            $editForm = $this->createForm(new ConfiguracionFechaType(), $entity);
        elseif($entity->getValorTipo()=='integer')
            $editForm = $this->createForm(new ConfiguracionNumeroType(), $entity);
        elseif($entity->getValorTipo()=='password')
            $editForm = $this->createForm(new ConfiguracionPasswordType(), $entity);
        elseif($entity->getValorTipo()=='string')
            $editForm = $this->createForm(new ConfiguracionStringType(), $entity);
        else
            $editForm = $this->createForm(new ConfiguracionType(), $entity);
        
        $editForm->submit($request);
        $nm = $this->get("notification.manager");

        if ($editForm->isValid()) {
            if($entity->getValorTipo()=='datetime')
            {
                $datetime = $entity->getValorPlano();
                $entity->setValor($datetime->format('Y-m-d H:i:s'));
            }
            $em->persist($entity);
            $em->flush();

            $nm->createNotificacion('El parámetro de ajuste se ha modificado con éxito.',
                                            Notificacion::USER_SUCCESS
                                            );
        }
        else
        {
            $nm->createNotificacion('El valor no es válido',
                                            Notificacion::USER_ERROR
                                            );
        }
        
        $sitio = explode('_',$entity->getSitio()->getSite());
        
        $ch = $this->get("context.helper");  
        $sitio = $ch->lowerizeText($sitio[1]);
        
        return $this->redirect($this->generateUrl('configuracion',array('sitio'=>$sitio)));
    }
}
