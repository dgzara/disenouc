<?php

namespace pDev\PracticasBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use pDev\PracticasBundle\Entity\EvaluacionProfesor;
use pDev\PracticasBundle\Entity\EvaluacionSupervisor;
use pDev\PracticasBundle\Entity\AlumnoPracticante;
use pDev\PracticasBundle\Entity\Criterio;
use pDev\PracticasBundle\Entity\CriterioTipo;
use pDev\PracticasBundle\Form\EvaluacionProfesorType;
use pDev\PracticasBundle\Form\EvaluacionSupervisorType;
use pDev\PracticasBundle\Form\EvaluacionProfesorDescuentoType;

/**
 * Evaluacion controller.
 *
 * @Route("/practicas/evaluacion/supervisor")
 */
class EvaluacionSupervisorController extends Controller
{
    /**
     * Lists all Evaluacion entities.
     *
     * @Route("/", name="practicas_evaluacion_supervisor")
     * @Template()
     */
    public function indexAction()
    {
        $pm = $this->get('permission.manager');
        $user = $pm->getUser();        
        $isCoordinacion = $pm->isGranted("ROLE_ADMIN","SITE_PRACTICAS");        
        $isAcademico = $pm->checkType("TYPE_ACADEMICO");
        $isSupervisor = $pm->checkType("TYPE_PRACTICAS_SUPERVISOR");
               
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('pDevPracticasBundle:AlumnoPracticante');
        $consulta = $repo->createQueryBuilder('p')
                    ->leftJoin('p.alumno','a')
                    ->leftJoin('a.periodos','periodo')
                    ->leftJoin('p.supervisor','s')
                    ->leftJoin('p.organizacion','o')                                        
                    ->leftJoin('p.profesor','prof')
                    ->leftJoin('p.profesorEvaluacion','evaluacion');
      
        if($isAcademico)
            $consulta->where('prof.id = :profesor')->setParameter('profesor', $user->getPersona('TYPE_ACADEMICO'));
        elseif($isSupervisor)
            $consulta->where('supervisor.id = :supervisor')->setParameter('supervisor', $user->getPersona('TYPE_PRACTICAS_SUPERVISOR'));
                    
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $consulta,
            $this->get('request')->query->get('page', 1)/*page number*/,
            10/*limit per page*/
        );
  
        return array(
            'pagination' => $pagination,    
            'isCoordinacion' => $isCoordinacion,
            'isAcademico' => $isAcademico, 
            'isSupervisor' => $isSupervisor   
        );
    }
    
    /**
     * Displays a form to create a new CriterioTipo entity.
     *
     * @Route("/create/{idPracticante}", name="practicas_evaluacion_supervisor_create")
     * @Method("POST")
     * @Template("pDevPracticasBundle:EvaluacionSupervisor:new.html.twig")
     */
    public function createAction(Request $request, $idPracticante)
    {
        $em = $this->getDoctrine()->getManager();
        
        $practicante = $em->getRepository('pDevPracticasBundle:AlumnoPracticante')->find($idPracticante);
        
        if(!$practicante){
            throw $this->createNotFoundException('Unable to find AlumnoPracticante entity.');
        }
        
        $entity = new EvaluacionSupervisor();
        $entity->setPractica($practicante);
        
        $criteriosTipo = $em->getRepository('pDevPracticasBundle:CriterioTipo')->findBy(array('tipoPractica'=>$practicante->getTipo(), 'tipoEvaluador'=>'Supervisor'));
        foreach($criteriosTipo as $criterioTipo)
        {
            $criterio = new Criterio();
            $criterio->setCriterioTipo($criterioTipo);
            $criterio->setEvaluacion($entity);
            $entity->addCriterio($criterio);
        }
        
        $form   = $this->createForm(new EvaluacionSupervisorType(), $entity);
        $form->submit($request);
        
        if($form->isValid()) 
        {
            // Cambiamos el estado de la practica
            $practicante->setEstado(AlumnoPracticante::ESTADO_INFORME);
            
            $em->persist($practicante);
            $em->persist($entity);
            $em->flush();
            
            $request->getSession()->getFlashBag()->add(
                'notice',
                'Ha sido realizada la evalaución'
            );
                
            return $this->redirect($this->generateUrl('practicas_evaluacion_supervisor_show', array('id' => $entity->getId())));
        }
        
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }
    
    /**
     * Displays a form to create a new CriterioTipo entity.
     *
     * @Route("/new/{idPracticante}", name="practicas_evaluacion_supervisor_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction($idPracticante)
    {
        $em = $this->getDoctrine()->getManager();
        
        $practicante = $em->getRepository('pDevPracticasBundle:AlumnoPracticante')->find($idPracticante);
        
        if(!$practicante){
            throw $this->createNotFoundException('Unable to find AlumnoPracticante entity.');
        }
        
        $entity = new EvaluacionSupervisor();
        $entity->setPractica($practicante);
        
        $criteriosTipo = $em->getRepository('pDevPracticasBundle:CriterioTipo')->findBy(array('tipoPractica'=>$practicante->getTipo(), 'tipoEvaluador'=>'Supervisor'));
        foreach($criteriosTipo as $criterioTipo)
        {
            $criterio = new Criterio();
            $criterio->setCriterioTipo($criterioTipo);
            $criterio->setEvaluacion($entity);
            $entity->addCriterio($criterio);
        }
        
        $form   = $this->createForm(new EvaluacionSupervisorType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }
    
    /**
     * Finds and displays a Evaluacion entity.
     *
     * @Route("/{id}/show", name="practicas_evaluacion_supervisor_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('pDevPracticasBundle:Evaluacion')->find($id);
        
        if(!$entity){
            throw $this->createNotFoundException('Unable to find Evaluacion entity.');
        }
        
        return array(
            'entity' => $entity,
        );
    }
    
    /**
     * Displays a form to edit an existing Evaluacion entity.
     *
     * @Route("/{id}/edit", name="practicas_evaluacion_supervisor_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('pDevPracticasBundle:Evaluacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Evaluacion entity.');
        }

        $editForm = $this->createForm(new EvaluacionType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Evaluacion entity.
     *
     * @Route("/{id}", name="practicas_evaluacion_supervisor_update")
     * @Method("PUT")
     * @Template("pDevPracticasBundle:EvaluacionSupervisor:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('pDevPracticasBundle:Evaluacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Evaluacion entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new EvaluacionType(), $entity);
        $editForm->submit($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('practicas_evaluacion_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Evaluacion entity.
     *
     * @Route("/{id}", name="practicas_evaluacion_supervisor_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->submit($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('pDevPracticasBundle:Evaluacion')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Evaluacion entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('practicas_evaluacion'));
    }

    /**
     * Creates a form to delete a Evaluacion entity by id.
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
