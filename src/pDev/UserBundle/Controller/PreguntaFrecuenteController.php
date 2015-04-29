<?php

namespace pDev\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use pDev\UserBundle\Entity\PreguntaFrecuente;
use pDev\UserBundle\Form\PreguntaFrecuenteType;

/**
 * PreguntaFrecuente controller.
 * @Route("/preguntafrecuente")
 *
 */
class PreguntaFrecuenteController extends Controller
{

    /**
     * Lists all PreguntaFrecuente entities.
     *
     * @Route("/", name="preguntafrecuente")
     * @Template()
     */
    public function indexAction()
    {
        $pm = $this->get('permission.manager');
        $user = $pm->getUser();
        $isCoordinacion = $pm->isGranted("ROLE_ADMIN","SITE_PRACTICAS");
        
        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository('pDevUserBundle:PreguntaFrecuente')->createQueryBuilder('p')
                    ->orderBy('p.id');
        
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1)/*page number*/,
            10/*limit per page*/
        );
        
        return array(
            'pagination' => $pagination,
            'isCoordinacion'    =>$isCoordinacion,
        );
    }
    /**
     * Creates a new PreguntaFrecuente entity.
     *
     * @Route("/create", name="preguntafrecuente_create")
     * @Method("POST")
     * @Template("pDevUserBundle:PreguntaFrecuente:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new PreguntaFrecuente();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('preguntafrecuente_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a PreguntaFrecuente entity.
     *
     * @param PreguntaFrecuente $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(PreguntaFrecuente $entity)
    {
        $form = $this->createForm(new PreguntaFrecuenteType(), $entity, array(
            'action' => $this->generateUrl('preguntafrecuente_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar', 'attr' => array('class' => 'btn btn-primary')));

        return $form;
    }

    /**
     * Displays a form to create a new PreguntaFrecuente entity.
     *
     * @Route("/new", name="preguntafrecuente_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new PreguntaFrecuente();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a PreguntaFrecuente entity.
     *
     * @Route("/{id}/show", name="preguntafrecuente_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('pDevUserBundle:PreguntaFrecuente')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PreguntaFrecuente entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing PreguntaFrecuente entity.
     *
     * @Route("/{id}/edit", name="preguntafrecuente_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('pDevUserBundle:PreguntaFrecuente')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PreguntaFrecuente entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a PreguntaFrecuente entity.
    *
    * @param PreguntaFrecuente $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(PreguntaFrecuente $entity)
    {
        $form = $this->createForm(new PreguntaFrecuenteType(), $entity, array(
            'action' => $this->generateUrl('preguntafrecuente_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar', 'attr' => array('class' => 'btn btn-primary')));

        return $form;
    }
    /**
     * Edits an existing PreguntaFrecuente entity.
     *
     * @Route("/{id}", name="preguntafrecuente_update")
     * @Method("PUT")
     * @Template("pDevUserBundle:PreguntaFrecuente:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('pDevUserBundle:PreguntaFrecuente')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PreguntaFrecuente entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('preguntafrecuente_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    
    /**
     * Displays a modal to delete an existing Practica entity.
     *
     * @Route("/{id}/delete/modal", name="preguntafrecuente_delete_modal")
     * @Method("GET")
     * @Template()
     */
    public function deleteModalAction($id)
    {
        $pm = $this->get('permission.manager');
        $isCoordinacion = $pm->isGranted("ROLE_ADMIN","SITE_PRACTICAS");
        
        // Cargamos la entidad
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('pDevUserBundle:PreguntaFrecuente')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PreguntaFrecuente entity.');
        }
        
        // Revisamos que sea el coordinador
        if(!$isCoordinacion){
            return $this->redirect($this->generateUrl('preguntafrecuente_show', array('id' => $id)));
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }
    
    /**
     * Deletes a PreguntaFrecuente entity.
     *
     * @Route("/{id}/delete", name="preguntafrecuente_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('pDevUserBundle:PreguntaFrecuente')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find PreguntaFrecuente entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('preguntafrecuente'));
    }

    /**
     * Creates a form to delete a PreguntaFrecuente entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('preguntafrecuente_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
