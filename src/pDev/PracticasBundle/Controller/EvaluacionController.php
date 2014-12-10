<?php

namespace pDev\PracticasBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use pDev\PracticasBundle\Entity\EvaluacionProfesor;
use pDev\PracticasBundle\Form\EvaluacionProfesorAsignarType;
use pDev\PracticasBundle\Form\EvaluacionProfesorDescuentoType;
use pDev\PracticasBundle\Entity\AlumnoPracticante;
use pDev\PracticasBundle\Entity\ProfesorEvaluador;
use pDev\PracticasBundle\Entity\Criterio;
use pDev\PracticasBundle\Entity\CriterioTipo;

/**
 * Evaluacion controller.
 *
 * @Route("/practicas/evaluacion")
 */
class EvaluacionController extends Controller
{
    /**
     * Lists all Evaluacion entities.
     *
     * @Route("/todas/{periodo}/{page}/{orderBy}/{order}", name="practicas_evaluacion")
     * @Template()
     */
    public function indexAction($periodo = null, $page = null, $orderBy = null, $order = null)
    {
        $pm = $this->get('permission.manager');
        $user = $pm->getUser();        
        
        if(!$periodo)
        {
            $request = $this->getRequest();
            $ch = $this->get("context.helper");    
            $year = $ch->getYearActual();
            $semestre = $ch->getSemestreActual();

            $periodo = $year.'-'.$semestre;
            $periodoform = $this->createPeriodForm($periodo);

            if ($request->isMethod('POST'))
            {
                $periodoform->bind($request);

                if ($periodoform->isValid())
                {
                    $periodo = ((string)$periodoform['periodo']->getData());                    
                }
            }
            
            return $this->redirect($this->generateUrl('practicas_evaluacion',array('periodo'=>$periodo)));
        }
        
        $periodo2 = explode('-', $periodo);
                                
        if(count($periodo2)==2)
        {
            $year = intval($periodo2[0]);
            $semestre = intval($periodo2[1]);
            $periodo = $year.'-'.$semestre;
        }
        
        $periodo_form = $this->createPeriodForm($periodo);

        $isCoordinacion = $pm->isGranted("ROLE_ADMIN","SITE_PRACTICAS");        
        
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('pDevPracticasBundle:AlumnoPracticante');
        $consulta = $repo->createQueryBuilder('p')
                    ->leftJoin('p.alumno','a')
                    ->leftJoin('a.periodos','periodo')
                    ->leftJoin('p.supervisor','s')
                    ->leftJoin('p.organizacionAlias','oa')                                        
                    ->leftJoin('p.profesor','prof')
                    ->leftJoin('prof.profesor','prof2')
                    ->leftJoin('p.profesorEvaluacion','evaluacion')
                    ->where('periodo.semestre = :semestre and periodo.year = :year');
                    //->andWhere('p.estado = :estado1 or p.estado = :estado2');
        $practicantes = array();
        
        if($isCoordinacion)
        {
            // con informe entregado o evaluados
            $consulta = $consulta->setParameter('semestre', $semestre);
            $consulta = $consulta->setParameter('year', $year);
            
        }
              
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $consulta,
            $this->get('request')->query->get('page', 1)/*page number*/,
            10/*limit per page*/
        );
  
        return array(
            'pagination' => $pagination,        
            'period_form'=>$periodo_form->createView(),
        );
    }
    
    /**
     * Lists all Evaluacion entities.
     *
     * @Route("/excel/{periodo}", name="practicas_evaluacion_excel")
     * @Template()
     */
    public function excelAction(Request $request)
    {
        $entities = $consulta->orderBy($orderBy, $order)                    
                ->getQuery()
                ->getResult();
        $excelService = $this->get('xls.service_xls2007');

        $excelService->excelObj->getProperties()->setCreator($user->getNombrecompleto())
                            ->setTitle('Practicas')
                            ->setSubject('');

        $excelService->excelObj->setActiveSheetIndex(0);
        $ec = 0;
        $ef = 1;

        $excelService->excelObj->getActiveSheet()->setCellValueByColumnAndRow($ec, $ef,'ALUMNO');
        $ec++;
        $excelService->excelObj->getActiveSheet()->setCellValueByColumnAndRow($ec, $ef,'NUMERO ALUMNO');
        $ec++;
        $excelService->excelObj->getActiveSheet()->setCellValueByColumnAndRow($ec, $ef,'TIPO');
        $ec++;
        $excelService->excelObj->getActiveSheet()->setCellValueByColumnAndRow($ec, $ef,'ORGANIZACION');
        $ec++;
        $excelService->excelObj->getActiveSheet()->setCellValueByColumnAndRow($ec, $ef,'FECHA INICIO');
        $ec++;
        $excelService->excelObj->getActiveSheet()->setCellValueByColumnAndRow($ec, $ef,'FECHA TERMINO');
        $ec++;
        $excelService->excelObj->getActiveSheet()->setCellValueByColumnAndRow($ec, $ef,'ESTADO');
        $ec++;
        $excelService->excelObj->getActiveSheet()->setCellValueByColumnAndRow($ec, $ef,'NOTA');
        $ec++;

        $ef++;
        $ec = 0;    
        foreach($entities as $entity)
        {
            $excelService->excelObj->getActiveSheet()->setCellValueByColumnAndRow($ec, $ef, $entity->getAlumno()->getNombreCompleto());
            $ec++;
            $excelService->excelObj->getActiveSheet()->setCellValueByColumnAndRow($ec, $ef, $entity->getAlumno()->getNumeroAlumno());
            $ec++;
            $excelService->excelObj->getActiveSheet()->setCellValueByColumnAndRow($ec, $ef, $entity->getTipo());
            $ec++;
            $excelService->excelObj->getActiveSheet()->setCellValueByColumnAndRow($ec, $ef, $entity->getOrganizacionAlias()->getNombre());
            $ec++;
            $excelService->excelObj->getActiveSheet()->setCellValueByColumnAndRow($ec, $ef,date_format($entity->getFechaInicio(),'d-m-Y'));
            $ec++;
            $excelService->excelObj->getActiveSheet()->setCellValueByColumnAndRow($ec, $ef,date_format($entity->getFechaTermino(),'d-m-Y'));
            $ec++;
            $excelService->excelObj->getActiveSheet()->setCellValueByColumnAndRow($ec, $ef, $entity->getEstado());
            $ec++;
            $nota = $entity->getProfesorEvaluacion()?$entity->getProfesorEvaluacion()->getNotaFinal():0.0;
            
            $excelService->excelObj->getActiveSheet()->setCellValueByColumnAndRow($ec, $ef, $nota);
            $ec++;

            $ef++;
            $ec = 0;
        }

        $nombrearchivo = 'exportar';

        $response = $excelService->getResponse();
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment;filename='.$nombrearchivo.'.xlsx');

        // If you are using a https connection, you have to set those two headers for compatibility with IE <9
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        return $response;
    }
    
    /**
     * Creates a new Evaluacion entity.
     *
     * @Route("/", name="practicas_evaluacion_create")
     * @Method("POST")
     * @Template("pDevPracticasBundle:Evaluacion:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new EvaluacionProfesor();
        
        $form = $this->createForm(new EvaluacionProfesorNewType());
        $form->submit($request);
        
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $profesor = $form->get('profesorOriginal')->getData();
            $profesorEvaluador = $em->getRepository('pDevPracticasBundle:ProfesorEvaluador')->findOneByProfesor($profesor->getId());
            if(!$profesorEvaluador)
            {
                $profesorEvaluador = new ProfesorEvaluador();
                $profesorEvaluador->setProfesor($profesor);
                $em->persist($profesorEvaluador);
            }
            $entity->setProfesor($profesorEvaluador);
            
            $practicantes = $form->get('practicantes');
            $tipo = $form->get('tipo')->getData();
                    
            $flag = null;
            foreach($practicantes as $practicante)
            {
                $numeroAlumno = $practicante->get('numeroAlumno')->getData();
                $alumno = $em->getRepository('pDevUserBundle:Alumno')->findOneByNumeroAlumno($numeroAlumno);
                if($alumno)
                {
                    $planpractica = $em->getRepository('pDevPracticasBundle:AlumnoPracticante')->findOneBy(array('alumno'=>$alumno->getId(),'estado'=>  AlumnoPracticante::ESTADO_INFORME));
                    if($planpractica)
                    {
                        $planpractica->setProfesor($profesorEvaluador);
                        $planpractica->setProfesorEvaluacion($entity);
                        $em->persist($planpractica);                        
                        $entity->setTipo($tipo);
                    }
                    else
                    {
                        $flag = 'No se encontró el plan de práctica con informe entregado para alumno número '.$numeroAlumno;
                    }
                }
                else 
                {
                    $flag = 'No se encontró el alumno número '.$numeroAlumno;

                }
            }
            
            if(!$flag)
            {
                $criterios = $em->getRepository('pDevPracticasBundle:CriterioTipo')->findBy(array('tipoPractica'=>$tipo,'tipoEvaluador'=>'Profesor'));
                
                foreach($criterios as $criterioTipo)
                {
                    $criterio = new Criterio();
                    $criterio->setCriterioTipo($criterioTipo);
                    $criterio->setEvaluacion($entity);
                    $em->persist($criterio);
                
                }
                
                $em->persist($entity);
                $em->flush();
                
                return $this->redirect($this->generateUrl('practicas_evaluacion_show', array('id' => $entity->getId())));
            }
            $nm = $this->get('notification.manager');
            $nm->createNotificacion('Evaluación realizada', $flag, \pDev\UserBundle\Entity\Notificacion::USER_ALERT);
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }


    /**
     * Displays a form to create a new Evaluacion entity.
     *
     * @Route("/{idPracticante}/descuento", name="practicas_evaluacion_descuento")
     * @Template()
     */
    public function descuentoAction($idPracticante)
    {
        $em = $this->getDoctrine()->getManager();
        
        $practicante = $em->getRepository('pDevPracticasBundle:AlumnoPracticante')->find($idPracticante);
        
        if(!$practicante){
            throw $this->createNotFoundException('Unable to find AlumnoPracticante entity.');
        }
        
        $evaluacionProfesor = $practicante->getProfesorEvaluacion();
        if(!$evaluacionProfesor)
        {
            throw $this->createNotFoundException('Unable to find evaluacionProfesor entity.');
        }
        
        $form   = $this->createForm(new EvaluacionProfesorDescuentoType(), $evaluacionProfesor);
        $request = $this->getRequest();
        if($request->isMethod('POST'))
        {
            $form->submit($request);
        
            if ($form->isValid()) 
            {
                $em->persist($evaluacionProfesor);
                $em->flush();
                
                return $this->redirect($this->generateUrl('practicas_evaluacion_show_profesor',array('idPracticante'=>$idPracticante)));
            }
        
        }
        
        return array(
            'evaluacionProfesor' => $evaluacionProfesor,
            'form'   => $form->createView(),     
            'idPracticante'=> $idPracticante
        );
    }
    
    /**
     * Displays a form to create a new Evaluacion entity.
     *
     * @Route("/{idPracticante}/asignar", name="practicas_evaluacion_asignar")
     * @Template()
     */
    public function asignarProfesorAction($idPracticante)
    {
        $em = $this->getDoctrine()->getManager();
        
        $practicante = $em->getRepository('pDevPracticasBundle:AlumnoPracticante')->find($idPracticante);
        
        if(!$practicante){
            throw $this->createNotFoundException('Unable to find AlumnoPracticante entity.');
        }
        
        $evaluacionProfesor = $practicante->getProfesorEvaluacion();
        if(!$evaluacionProfesor)
            $evaluacionProfesor = new EvaluacionProfesor();
        
        $form   = $this->createForm(new EvaluacionProfesorAsignarType(), $evaluacionProfesor);
        $request = $this->getRequest();
        if($request->isMethod('POST'))
        {
            $form->submit($request);
        
            if ($form->isValid()) {
                $profesor = $form->get('profesorOriginal')->getData();
                $profesorEvaluador = $em->getRepository('pDevPracticasBundle:ProfesorEvaluador')->findOneByProfesor($profesor->getId());
                if(!$profesorEvaluador)
                {
                    $profesorEvaluador = new ProfesorEvaluador();
                    $profesorEvaluador->setProfesor($profesor);
                    $em->persist($profesorEvaluador);
                }
                $evaluacionProfesor->setProfesor($profesorEvaluador);
                $em->persist($evaluacionProfesor);
                
                $practicante->setProfesor($profesorEvaluador);
                $practicante->setProfesorEvaluacion($evaluacionProfesor);
                $em->persist($practicante);  
                
                $tipo = $practicante->getTipo();
                $evaluacionProfesor->setTipo($practicante->getTipo());
                
                $criteriosTipo = $em->getRepository('pDevPracticasBundle:CriterioTipo')->findBy(array('tipoPractica'=>$tipo,'tipoEvaluador'=>'Profesor'));
                $criteriosOriginales = $evaluacionProfesor->getCriterios();
                                
                foreach($criteriosTipo as $criterioTipo)
                {                    
                    $existe = false;
                    
                    foreach($criteriosOriginales as $criterioOriginal)
                    {
                        if($criterioTipo->getId()==$criterioOriginal->getCriterioTipo()->getId())
                        {
                            $existe = true;
                            break;
                        }
                    }
                    
                    if(!$existe)
                    {
                        $criterio = new Criterio();
                        $criterio->setCriterioTipo($criterioTipo);
                        $criterio->setEvaluacion($evaluacionProfesor);
                        $em->persist($criterio);                
                    }
                }
                
                $em->flush();
                
                return $this->redirect($this->generateUrl('practicas_evaluacion'));
            }
        }
        
        return array(
            'evaluacionProfesor' => $evaluacionProfesor,
            'form'   => $form->createView(),
            'idPracticante'=>$practicante->getId()
        );
    }

    /**
     * Finds and displays a Evaluacion entity.
     *
     * @Route("/{idPracticante}/supervisor", name="practicas_evaluacion_show_supervisor")
     * @Method("GET")
     * @Template()
     */
    public function showSupervisorAction($idPracticante)
    {
        $em = $this->getDoctrine()->getManager();

        $practicante = $em->getRepository('pDevPracticasBundle:AlumnoPracticante')->find($idPracticante);
        
        if(!$practicante){
            throw $this->createNotFoundException('Unable to find AlumnoPracticante entity.');
        }
        
        $evaluacionProfesor = $practicante->getProfesorEvaluacion();
        $evaluacionSupervisor = $practicante->getSupervisorEvaluacion();

        return array(
            'evaluacion_profesor'      => $evaluacionProfesor,
            'evaluacion_supervisor'      => $evaluacionSupervisor,            
        );
    }
    
    /**
     * Finds and displays a Evaluacion entity.
     *
     * @Route("/{idPracticante}/profesor", name="practicas_evaluacion_show_profesor")
     * @Method("GET")
     * @Template()
     */
    public function showProfesorAction($idPracticante)
    {
        $em = $this->getDoctrine()->getManager();

        $practicante = $em->getRepository('pDevPracticasBundle:AlumnoPracticante')->find($idPracticante);
        
        if(!$practicante){
            throw $this->createNotFoundException('Unable to find AlumnoPracticante entity.');
        }
        
        $evaluacionProfesor = $practicante->getProfesorEvaluacion();
        $evaluacionSupervisor = $practicante->getSupervisorEvaluacion();

        return array(
            'evaluacion_profesor'      => $evaluacionProfesor,
            'evaluacion_supervisor'      => $evaluacionSupervisor, 
            'idPracticante'             => $idPracticante
        );
    }

    /**
     * Displays a form to edit an existing Evaluacion entity.
     *
     * @Route("/{id}/edit", name="practicas_evaluacion_edit")
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
     * @Route("/{id}", name="practicas_evaluacion_update")
     * @Method("PUT")
     * @Template("pDevPracticasBundle:Evaluacion:edit.html.twig")
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
     * @Route("/{id}", name="practicas_evaluacion_delete")
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
    
    private function createPeriodForm($data = null, $tooltip='Periodo académico')
    {
        $attr = array();
        //$attr = array('placeholder'=>$placeholder);
        if($tooltip)
        {
            $attr['title']=$tooltip;
            $attr['data-toggle']='tooltip';
        }
        
        $periodos = array();
        $ch = $this->get("context.helper");    
        $year = $ch->getYearActual();
        $semestre = $ch->getSemestreActual();
        
        while($year>1969)
        {
            $periodo = $year.'-'.$semestre--;
            $periodos[$periodo] = $periodo;
            if($semestre==0)
            {
                $semestre=2;
                $year--;
            }
        }
        
        return $this->createFormBuilder()
            ->add('periodo', 'choice',array('label'=>'Periodo académico','attr' => $attr,
                    'choices' => $periodos,'data' => $data))
 
            ->getForm()
        ;
    }
}
