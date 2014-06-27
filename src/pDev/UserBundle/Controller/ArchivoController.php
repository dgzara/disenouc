<?php

namespace pDev\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use pDev\UserBundle\Entity\Archivo;
use pDev\UserBundle\Form\ArchivoType;


/**
 * Archivo controller.
 *
 * @Route("/archivo")
 */
class ArchivoController extends Controller
{

    /**
     * Gets a Archivo entity.
     *
     * @Route("/{idArchivo}", name="archivo_get")
     * @Template()
     */
    public function getArchivoAction($idArchivo)
    {
        $pm = $this->get("permission.manager");
        
        $em = $this->getDoctrine()->getManager();
        $archivo = null;
    
        if($idArchivo==='causales')
            $archivo = $em->getRepository('pDevUserBundle:Archivo')->findOneByPath('plantilla_causales.xlsx');
        else if($idArchivo==='personas')
            $archivo = $em->getRepository('pDevUserBundle:Archivo')->findOneByPath('plantilla_personas.xlsx');
        else
            $archivo = $em->getRepository('pDevUserBundle:Archivo')->find($idArchivo);

        if (!$archivo) {
            throw $this->createNotFoundException('Unable to find Archivo entity.');
        }
        
        $isOwner = $archivo->getOwner()->getId() === $pm->getUser()->getId();
        $isGranted = $pm->isGranted('ROLE_SUPER_USER',$archivo->getSite()->getSite());
        $pm->throwForbidden(!$isOwner and !$isGranted);
        
        
        $response = new BinaryFileResponse($archivo->getAbsolutePath());
        $response->headers->set('Content-Type', $archivo->getMimetype());
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $archivo->getPath());
        return $response;
    }
    
    
    
    /**
     * Lists all Archivo entities.
     *
     * @Route("/", name="archivo")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $this->get("permission.manager")->throwForbidden();
        
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('pDevUserBundle:Archivo')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Archivo entity.
     *
     * @Route("/", name="archivo_create")
     * @Method("POST")
     * @Template("pDevUserBundle:Archivo:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $this->get("permission.manager")->throwForbidden();
        
        $entity  = new Archivo();
        $form = $this->createForm(new ArchivoType(), $entity);
        $form->submit($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('archivo_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Archivo entity.
     *
     * @Route("/new", name="archivo_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $this->get("permission.manager")->throwForbidden();
        $entity = new Archivo();
        $form   = $this->createForm(new ArchivoType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Archivo entity.
     *
     * @Route("/{id}", name="archivo_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $this->get("permission.manager")->throwForbidden();
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('pDevUserBundle:Archivo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Archivo entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Archivo entity.
     *
     * @Route("/{id}/edit", name="archivo_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $this->get("permission.manager")->throwForbidden();
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('pDevUserBundle:Archivo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Archivo entity.');
        }

        $editForm = $this->createForm(new ArchivoType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Archivo entity.
     *
     * @Route("/{id}", name="archivo_update")
     * @Method("PUT")
     * @Template("pDevUserBundle:Archivo:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $this->get("permission.manager")->throwForbidden();
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('pDevUserBundle:Archivo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Archivo entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new ArchivoType(), $entity);
        $editForm->submit($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('archivo_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Archivo entity.
     *
     * @Route("/{id}", name="archivo_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $this->get("permission.manager")->throwForbidden();
        $form = $this->createDeleteForm($id);
        $form->submit($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('pDevUserBundle:Archivo')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Archivo entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('archivo'));
    }

    /**
     * Creates a form to delete a Archivo entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
