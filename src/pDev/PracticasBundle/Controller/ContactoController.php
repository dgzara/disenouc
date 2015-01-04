<?php

namespace pDev\PracticasBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use pDev\PracticasBundle\Entity\Contacto;
use pDev\PracticasBundle\Form\ContactoType;

/**
 * Contacto controller.
 *
 * @Route("/practicas/contacto")
 */
class ContactoController extends Controller
{

    /**
     * Lists all Contacto entities.
     *
     * @Route("/", name="practicas_contacto")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $dql   = "SELECT s FROM pDevPracticasBundle:Contacto s";
        $query = $em->createQuery($dql);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1)/*page number*/,
            10/*limit per page*/
        );
        
        return array(
            'pagination' => $pagination,
        );
    }
    
    /**
     * Creates a new Contacto entity.
     *
     * @Route("/", name="practicas_contacto_create")
     * @Method("POST")
     * @Template("pDevPracticasBundle:Contacto:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Contacto();
        $form = $this->createForm(new ContactoType(), $entity);
        $form->submit($request);

        if ($form->isValid()) 
        {
            $em = $this->getDoctrine()->getManager();
            
            // Revisamos si hay un usuario con los mismos datos
            $usuario = $em->getRepository('pDevUserBundle:User')->findOneByEmail($entity->getEmail());
            
            // Probamos con el rut
            if(!$usuario)
                $usuario = $em->getRepository('pDevUserBundle:User')->findOneByRut($entity->getRut()); 
            
            // Si lo encuentra, lo añade
            if($usuario)
                $entity->setUsuario($usuario);
                
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('practicas_contacto_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Contacto entity.
     *
     * @Route("/new", name="practicas_contacto_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Contacto();
        $form   = $this->createForm(new ContactoType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Contacto entity.
     *
     * @Route("/{id}/show", name="practicas_contacto_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('pDevPracticasBundle:Contacto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Contacto entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Contacto entity.
     *
     * @Route("/{id}/edit", name="practicas_contacto_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('pDevPracticasBundle:Contacto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Contacto entity.');
        }

        $editForm = $this->createForm(new ContactoType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Contacto entity.
     *
     * @Route("/{id}", name="practicas_contacto_update")
     * @Method("PUT")
     * @Template("pDevPracticasBundle:Contacto:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('pDevPracticasBundle:Contacto')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Contacto entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new ContactoType(), $entity);
        $editForm->submit($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('practicas_contacto_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    
    /**
     * Deletes a Contacto entity.
     *
     * @Route("/{id}", name="practicas_contacto_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->submit($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('pDevPracticasBundle:Contacto')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Contacto entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('practicas_contacto'));
    }
    
    /**
     * Lists all Alumnos entities.
     *
     * @Route("/contacto/buscar", name="persona_contactoes_buscar")
     * @Method("POST")
     * @Template("pDevPracticaBundle:Contacto:index.html.twig")
     */
    public function contactoesBuscarAction()
    {
        $pm = $this->get("permission.manager");
        $pm->isGrantedForbidden('ROLE_SUPER_USER',"SITE_SUPERVISOR");
                
        $sh = $this->get("search.helper");
        $searchform = $this->createSearchPersonasForm('Buscar contactoes', 'números de alumno');
        $request = $this->getRequest();
        
        if ($request->isMethod('POST'))
        {
            $searchform->bind($request);
            
            if ($searchform->isValid())
            {
                $query = ((string)$searchform['querystring']->getData());                        
                $entitystring = 'pDevPracticaBundle:Contacto';            

                // preparamos la consulta
                $em = $this->getDoctrine()->getManager();
                $qb = $em->getRepository($entitystring)->createQueryBuilder('p');
                $qb = $qb->select('p');

                $totalcount = $sh->getEntitiesCount($entitystring);
                $fields = $sh->getPersonaFields(array('p.email'));
                $results = $sh->getResultados($fields,$query,$qb);

                return array(
                    'contactoes' => $results,
                    'total' => $totalcount,
                    'search_form' => $searchform->createView(),
                    'anterior'=> false,
                    'siguiente' => false
                );
            }
            
        }
        
        $nm = $this->get("notification.manager");
        $nm->createNotificacion('Error', 'Ocurrió un error, inténtelo más tarde.', Notificacion::USER_ERROR);
        return $this->redirect($this->generateUrl('practicas_contacto', array('page'=>1,'orderBy'=>'nombres','order'=>'asc')));
    }
    
    /**
     * Displays a form to create a new Organizacion entity.
     *
     * @Route("/find", name="practicas_contacto_find")
     * @Method("GET")
     */
    public function searchAction()
    {
        $return = array();
        
        $data = strtolower($this->get('request')->query->get('query'));

        $em = $this->getDoctrine()->getManager();


        $qb = $em->getRepository('pDevPracticasBundle:Contacto')->createQueryBuilder('p');
        $entities = $qb
                        ->where('p.rut like :rut')
                        ->setParameter('rut',$data.'%')
                        ->getQuery()
                        ->getResult();


        foreach ($entities as $entity)
        {
            $return[] = array(
                'label'=> $entity->getRut(),
                'value'=>$entity->getRut(),
                'nombres'=>$entity->getNombres(),
                'apellidoPaterno'=>$entity->getApellidoPaterno(),
                'apellidoMaterno'=>$entity->getApellidoMaterno(),
                'cargo'=>$entity->getCargo(),
                'email'=>$entity->getEmail(),
                
                );
        }
        
                 
        $response = new Response(json_encode($return));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    } 

    /**
     * Displays a form to create a new Organizacion entity.
     *
     * @Route("/lista.json", name="practicas_contacto_json")
     * @Method("GET")
     */
    public function jsonAction()
    {
        $return = array();
        
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('pDevPracticasBundle:Contacto')->findAll();

        foreach ($entities as $entity)
        {
            $return[] = array(
                'id' => $entity->getId(),
                'value' => $entity->__toString(),
                'apellidoPaterno'=>$entity->getApellidoPaterno(),
                'apellidoMaterno'=>$entity->getApellidoMaterno(),
                'email'=>$entity->getEmail(),
            );
        }
                 
        $response = new Response(json_encode($return));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
    
    /**
     * Creates a form to delete a Contacto entity by id.
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