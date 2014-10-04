<?php

namespace pDev\PracticasBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use pDev\PracticasBundle\Entity\Practica;
use pDev\PracticasBundle\Entity\Organizacion;
use pDev\PracticasBundle\Entity\OrganizacionAlias;
use pDev\PracticasBundle\Entity\Contacto;
use pDev\PracticasBundle\Form\PracticaType;
use pDev\PracticasBundle\Form\OrganizacionType;
use pDev\PracticasBundle\Form\OrganizacionAliasType;
use pDev\PracticasBundle\Form\ContactoType;
use pDev\PracticasBundle\Form\PracticaEstadoType;

/**
 * Practica controller.
 *
 * @Route("/practicas")
 */
class PracticaController extends Controller
{
    /**
     * Lists all Practica entities.
     *
     * @Route("/todas/{periodo}/{page}/{orderBy}/{order}", name="practicas")
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
            
            return $this->redirect($this->generateUrl('practicas',array('periodo'=>$periodo)));
        }
        
        $periodo2 = explode('-', $periodo);
                                
        if(count($periodo2)==2)
        {
            $year = intval($periodo2[0]);
            $semestre = intval($periodo2[1]);
            $periodo = $year.'-'.$semestre;
        }
        
        $periodo_form = $this->createPeriodForm($periodo);
        $fecha1 = $year.'-';
        $fecha2 = $year.'-';
        if($semestre == 2)
        {
            $fecha1 .= '07-15 00:00:00';
            $fecha2 .= '12-31 00:00:00';
        }
        else
        {
            $fecha1 .= '01-01 00:00:00';
            $fecha2 .= '07-14 00:00:00';
        }
        
        $isExterno = $user->getExternal();
        $isAlumno = $pm->checkType("TYPE_ALUMNO");
        $isSupervisor = $pm->checkType("TYPE_PRACTICAS_SUPERVISOR");
        $isContacto = $pm->checkType("TYPE_PRACTICAS_CONTACTO");
        $isCoordinacion = $pm->isGranted("ROLE_ADMIN","SITE_PRACTICAS");
        
        $em = $this->getDoctrine()->getManager();
        
        $qb = $em->getRepository('pDevPracticasBundle:Practica')->createQueryBuilder('p');
        $entities = $qb->leftJoin('p.creador','cr')
                    ->leftJoin('p.contacto','co')
                    ->leftJoin('p.organizacionAlias','oa')
                    ->where('p.fechaInicio >= :fecha1 and p.fechaInicio <= :fecha2');
        
        if(!$isCoordinacion)
        {
            $where = 'cr.id = :idUser';
            $entities = $entities->setParameter('idUser', $user->getId());

            if(!$isExterno)
            {
                $where .= ' or p.estado = :estado';
                $entities = $entities->setParameter('estado',Practica::ESTADO_APROBADA);

            }

            if($isContacto)
            {
                $contacto = $user->getPersona('TYPE_PRACTICAS_CONTACTO');
                if($contacto)
                {
                    $where .= ' or co.id = :coid';
                    $entities = $entities->setParameter('coid', $contacto->getId());
                }                
            }
            
            $entities = $entities->andWhere($where);   
        }
        
        $query = $entities->setParameter('fecha1', $fecha1)->setParameter('fecha2', $fecha2);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1)/*page number*/,
            10/*limit per page*/
        );

        return array(
            'pagination' => $pagination,
            'isContacto'    =>$isContacto,
            'isCoordinacion'    =>$isCoordinacion,
            'isAlumno'      =>  $isAlumno,
            'period_form' => $periodo_form->createView(),
        );
    }
    
    public function excelACtion(Request $request)
    {
        $entities = $entities->orderBy($orderBy, $order)                    
                ->getQuery()
                ->getResult();
        $excelService = $this->get('xls.service_xls2007');

        $excelService->excelObj->getProperties()->setCreator($user->getNombrecompleto())
                            ->setTitle('Practicas')
                            ->setSubject('');

        $excelService->excelObj->setActiveSheetIndex(0);
        $ec = 0;
        $ef = 1;

        $excelService->excelObj->getActiveSheet()->setCellValueByColumnAndRow($ec, $ef,'ID');
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

        $ef++;
        $ec = 0;    
        foreach($entities as $entity)
        {
            $excelService->excelObj->getActiveSheet()->setCellValueByColumnAndRow($ec, $ef, $entity->getId());
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
     * Creates a new Practica entity.
     *
     * @Route("/create", name="practicas_create")
     * @Method("POST")
     * @Template("pDevPracticasBundle:Practica:new.html.twig")
     */
    public function createAction(Request $request)
    {   
        $pm = $this->get('permission.manager');
        $user = $pm->getUser();
        $entity  = new Practica();
        
        // Si es un contacto, lo agrega dentro del formulario
        $isContacto = $pm->checkType("TYPE_PRACTICAS_CONTACTO");  
        if($isContacto){
            $contacto = $user->getPersona('TYPE_PRACTICAS_CONTACTO');
            $entity->setContacto($contacto);
        }
        
        $securityContext = $this->container->get('security.context');
        $form = $this->createForm(new PracticaType($securityContext), $entity);
        
        if($isContacto){
            $form->remove('contacto');
        }
        
        $form->handleRequest($request);

        if ($form->isValid()) 
        {   
            $em = $this->getDoctrine()->getManager();
            $entity->setCreador($this->getUser());
            
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('practicas_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Practica entity.
     *
     * @Route("/new", name="practicas_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $pm = $this->get('permission.manager');
        $user = $pm->getUser();
        
        $entity = new Practica();
        
        // Si es un contacto, lo agrega altiro
        $isContacto = $pm->checkType("TYPE_PRACTICAS_CONTACTO");  
        if($isContacto){
            $contacto = $user->getPersona('TYPE_PRACTICAS_CONTACTO');
            $entity->setContacto($contacto);
        }
        
        $securityContext = $this->container->get('security.context');
        $form = $this->createForm(new PracticaType($securityContext), $entity);
        
        if($isContacto){
            $form->remove('contacto');
            $form->remove('tipo');
        }
        
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Practica entity.
     *
     * @Route("/{id}/show", name="practicas_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $pm = $this->get('permission.manager');
        $user = $pm->getUser();
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('pDevPracticasBundle:Practica')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Practica entity.');
        }
        
        $alumnoPractica = false;
        $isExterno = $user->getExternal();
        $isAlumno = $pm->checkType("TYPE_ALUMNO");
        $isSupervisor = $pm->checkType("TYPE_PRACTICAS_SUPERVISOR") and $entity->hasSupervisor($user->getPersona('TYPE_PRACTICAS_SUPERVISOR'));
        $isContacto = $pm->checkType("TYPE_PRACTICAS_CONTACTO") and $entity->hasContacto($user->getPersona('TYPE_PRACTICAS_CONTACTO'));
        $isCoordinacion = $pm->isGranted("ROLE_ADMIN","SITE_PRACTICAS");
        
        // Si es alumno, comprobamos si ya postuló
        if($isAlumno)
            $alumnoPractica = $em->getRepository('pDevPracticasBundle:AlumnoPracticante')->findOneByAlumno($user->getPersona('TYPE_ALUMNO'));
        
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
            'isAlumno' => $isAlumno,
            'alumnoPractica' => $alumnoPractica,
            'isContacto' => $isContacto,
            'isSupervisor' => $isSupervisor,
            'isCoordinacion' => $isCoordinacion,
        );
    }

    /**
     * Displays a form to edit an existing Practica entity.
     *
     * @Route("/{id}/edit", name="practicas_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('pDevPracticasBundle:Practica')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Practica entity.');
        }

        $editForm = $this->createForm(new PracticaType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Practica entity.
     *
     * @Route("/{id}/update", name="practicas_update")
     * @Method("PUT")
     * @Template("pDevPracticasBundle:Practica:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('pDevPracticasBundle:Practica')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Practica entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new PracticaType(), $entity);
        $editForm->submit($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('practicas_show', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    
    /**
     * Displays a form to edit an existing Practica entity.
     *
     * @Route("/{id}/estado", name="practicas_estado")
     * @Template()
     */
    public function estadoAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('pDevPracticasBundle:Practica')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Practica entity.');
        }

        $editForm = $this->createForm(new PracticaEstadoType(), $entity);
        
        $request = $this->getRequest();
        if($request->isMethod('POST'))
        {
            $editForm->submit($request);

            if ($editForm->isValid()) {
                $em->persist($entity);
                $em->flush();
                
                // Devolvemos la respuesta
                $array = array('redirect' => $this->generateUrl('practicas_show', array('id' => $id))); // data to return via JSON
                $response = new Response( json_encode( $array ) );
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
        }
        
        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }
    
    /**
     * Displays a modal to delete an existing Practica entity.
     *
     * @Route("/{id}/delete/modal", name="practicas_delete_modal")
     * @Method("GET")
     * @Template()
     */
    public function deleteModalAction($id)
    {
        $pm = $this->get('permission.manager');
        $user = $pm->getUser();
        
        $isExterno = $user->getExternal();
        $isAlumno = $pm->checkType("TYPE_ALUMNO");
        $isSupervisor = $pm->checkType("TYPE_PRACTICAS_SUPERVISOR");
        $isContacto = $pm->checkType("TYPE_PRACTICAS_CONTACTO");
        $isCoordinacion = $pm->isGranted("ROLE_ADMIN","SITE_PRACTICAS");
        
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('pDevPracticasBundle:Practica')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Practica entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }
    
    /**
     * Deletes a Practica entity.
     *
     * @Route("/{id}/remove", name="practicas_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->submit($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('pDevPracticasBundle:Practica')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Practica entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('practicas'));
    }

    /**
     * Creates a form to delete a Practica entity by id.
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
