<?php

namespace pDev\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use pDev\UserBundle\Entity\Contacto;
use pDev\UserBundle\Form\ContactoType;

/**
 * Contacto controller.
 *
 * @Route("/contacto")
 */
class ContactoController extends Controller
{
    /**
     * Finds and displays a Contacto entity.
     *
     * @Route("/show", name="contacto_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('pDevUserBundle:Contacto')->find(1);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Contacto entity.');
        }

        return array(
            'entity'      => $entity,
        );
    }

    /**
     * Displays a form to edit an existing Contacto entity.
     *
     * @Route("/edit", name="contacto_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('pDevUserBundle:Contacto')->find(1);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Contacto entity.');
        }

        $editForm = $this->createEditForm($entity);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Contacto entity.
    *
    * @param Contacto $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Contacto $entity)
    {
        $form = $this->createForm(new ContactoType(), $entity, array(
            'action' => $this->generateUrl('contacto_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar', 'attr' => array('class' => 'btn btn-primary')));

        return $form;
    }
    /**
     * Edits an existing Contacto entity.
     *
     * @Route("/update", name="contacto_update")
     * @Method("PUT")
     * @Template("pDevUserBundle:Contacto:edit.html.twig")
     */
    public function updateAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('pDevUserBundle:Contacto')->find(1);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Contacto entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            
            $request->getSession()->getFlashBag()->add(
                'notice',
                'Página de contacto actualizada'
            );
            
            return $this->redirect($this->generateUrl('contacto_edit'));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }
}
