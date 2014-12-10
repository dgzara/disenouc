<?php

namespace pDev\PracticasBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use pDev\PracticasBundle\Entity\Supervisor;
use pDev\PracticasBundle\Form\SupervisorType;

/**
 * Supervisor controller.
 *
 * @Route("/practicas/supervisor")
 */
class SupervisorController extends Controller
{

    /**
     * Lists all Supervisor entities.
     *
     * @Route("/", name="practicas_supervisor")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $dql   = "SELECT s FROM pDevPracticasBundle:Supervisor s";
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
     * Creates a new Supervisor entity.
     *
     * @Route("/", name="practicas_supervisor_create")
     * @Method("POST")
     * @Template("pDevPracticasBundle:Supervisor:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Supervisor();
        $form = $this->createForm(new SupervisorType(), $entity);
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

            return $this->redirect($this->generateUrl('practicas_supervisor_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Supervisor entity.
     *
     * @Route("/new", name="practicas_supervisor_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Supervisor();
        $form   = $this->createForm(new SupervisorType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Supervisor entity.
     *
     * @Route("/{id}/show", name="practicas_supervisor_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('pDevPracticasBundle:Supervisor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Supervisor entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Supervisor entity.
     *
     * @Route("/{id}/edit", name="practicas_supervisor_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('pDevPracticasBundle:Supervisor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Supervisor entity.');
        }

        $editForm = $this->createForm(new SupervisorType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Supervisor entity.
     *
     * @Route("/{id}", name="practicas_supervisor_update")
     * @Method("PUT")
     * @Template("pDevPracticasBundle:Supervisor:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('pDevPracticasBundle:Supervisor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Supervisor entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new SupervisorType(), $entity);
        $editForm->submit($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('practicas_supervisor_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    
    /**
     * Deletes a Supervisor entity.
     *
     * @Route("/{id}", name="practicas_supervisor_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->submit($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('pDevPracticasBundle:Supervisor')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Supervisor entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('practicas_supervisor'));
    }
    
    /**
     * Lists all Alumnos entities.
     *
     * @Route("/supervisor/buscar", name="persona_supervisores_buscar")
     * @Method("POST")
     * @Template("pDevPracticaBundle:Supervisor:index.html.twig")
     */
    public function supervisoresBuscarAction()
    {
        $pm = $this->get("permission.manager");
        $pm->isGrantedForbidden('ROLE_SUPER_USER',"SITE_SUPERVISOR");
                
        $sh = $this->get("search.helper");
        $searchform = $this->createSearchPersonasForm('Buscar supervisores', 'números de alumno');
        $request = $this->getRequest();
        
        if ($request->isMethod('POST'))
        {
            $searchform->bind($request);
            
            if ($searchform->isValid())
            {
                $query = ((string)$searchform['querystring']->getData());                        
                $entitystring = 'pDevPracticaBundle:Supervisor';            

                // preparamos la consulta
                $em = $this->getDoctrine()->getManager();
                $qb = $em->getRepository($entitystring)->createQueryBuilder('p');
                $qb = $qb->select('p');

                $totalcount = $sh->getEntitiesCount($entitystring);
                $fields = $sh->getPersonaFields(array('p.email'));
                $results = $sh->getResultados($fields,$query,$qb);

                return array(
                    'supervisores' => $results,
                    'total' => $totalcount,
                    'search_form' => $searchform->createView(),
                    'anterior'=> false,
                    'siguiente' => false
                );
            }
            
        }
        
        $nm = $this->get("notification.manager");
        $nm->createNotificacion('Error', 'Ocurrió un error, inténtelo más tarde.', Notificacion::USER_ERROR);
        return $this->redirect($this->generateUrl('practicas_supervisor', array('page'=>1,'orderBy'=>'nombres','order'=>'asc')));
    }
    
    /**
     * Displays a form to create a new Organizacion entity.
     *
     * @Route("/find", name="practicas_supervisor_find")
     * @Method("GET")
     */
    public function searchAction()
    {
        $return = array();
        
        $data = strtolower($this->get('request')->query->get('query'));

        $em = $this->getDoctrine()->getManager();


        $qb = $em->getRepository('pDevPracticasBundle:Supervisor')->createQueryBuilder('p');
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
     * Creates a form to delete a Supervisor entity by id.
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
