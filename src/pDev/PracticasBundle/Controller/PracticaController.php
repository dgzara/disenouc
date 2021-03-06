<?php

namespace pDev\PracticasBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
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
    public function indexAction($periodo = null,$page = null,$orderBy = null,$order = null)
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
        
        if(!$page or !$orderBy or !$order)
        {
            return $this->redirect($this->generateUrl('practicas',array('periodo'=>$periodo,'page'=>1,'orderBy'=>'fechaInicio','order'=>'asc')));
        }
                
        if($orderBy!='tipo' and $orderBy!='organizacionAlias' and $orderBy!='fechaInicio' and $orderBy!='estado')
            throw $this->createNotFoundException();
        else
            $orderBy = 'p.'.$orderBy;
        if($order!='asc' and $order!='desc')
            throw $this->createNotFoundException();
        $excel = null;
        if($page ==='excel')
        {
            $page = 1;
            $excel = true;
        }
        $page = !$page?1:intval($page);
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
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
            $entities = $entities->setParameter('idUser',$user->getId());


            if(!$isExterno)
            {
                $where .= ' or p.estado = :estado';
                $entities = $entities->setParameter('estado',Practica::ESTADO_PUBLICADA);

            }

            if($isContacto)
            {
                $contacto = $user->getPersona('TYPE_PRACTICAS_CONTACTO');
                if($contacto)
                {
                    $where .= ' or co.id = :coid';
                    $entities = $entities->setParameter('coid',$contacto->getId());
                }                
            }
            
            $entities = $entities->andWhere($where);   
        }
        
        $entities = $entities->setParameter('fecha1', $fecha1)
            ->setParameter('fecha2', $fecha2);
        $entities2 = $entities->getQuery()
            ->getResult();
        $count = count($entities2);
        $anterior = $offset>0?$page-1:false;
        $siguiente = $page*$limit<$count?$page + 1:false;
        
        if($offset>$count or $page < 1)
        {
            throw $this->createNotFoundException();
        }
        
        if($orderBy==='p.organizacionAlias')
        {
            $orderBy = 'oa.nombre';            
        }
        
        if(!$excel)
        {
            $entities = $entities->orderBy($orderBy, $order)
                        ->setFirstResult( $offset )
                        ->setMaxResults( $limit )
                        ->getQuery()
                        ->getResult();
        }
        else
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

            $excelService->excelObj->getActiveSheet()->setCellValueByColumnAndRow($ec,$ef,'ID');
            $ec++;
            $excelService->excelObj->getActiveSheet()->setCellValueByColumnAndRow($ec,$ef,'TIPO');
            $ec++;
            $excelService->excelObj->getActiveSheet()->setCellValueByColumnAndRow($ec,$ef,'ORGANIZACION');
            $ec++;
            $excelService->excelObj->getActiveSheet()->setCellValueByColumnAndRow($ec,$ef,'FECHA INICIO');
            $ec++;
            $excelService->excelObj->getActiveSheet()->setCellValueByColumnAndRow($ec,$ef,'FECHA TERMINO');
            $ec++;
            $excelService->excelObj->getActiveSheet()->setCellValueByColumnAndRow($ec,$ef,'ESTADO');
            $ec++;

            $ef++;
            $ec = 0;    
            foreach($entities as $entity)
            {
                $excelService->excelObj->getActiveSheet()->setCellValueByColumnAndRow($ec,$ef,$entity->getId());
                $ec++;
                $excelService->excelObj->getActiveSheet()->setCellValueByColumnAndRow($ec,$ef,$entity->getTipo());
                $ec++;
                $excelService->excelObj->getActiveSheet()->setCellValueByColumnAndRow($ec,$ef,$entity->getOrganizacionAlias()->getNombre());
                $ec++;
                $excelService->excelObj->getActiveSheet()->setCellValueByColumnAndRow($ec,$ef,date_format($entity->getFechaInicio(),'d-m-Y'));
                $ec++;
                $excelService->excelObj->getActiveSheet()->setCellValueByColumnAndRow($ec,$ef,date_format($entity->getFechaTermino(),'d-m-Y'));
                $ec++;
                $excelService->excelObj->getActiveSheet()->setCellValueByColumnAndRow($ec,$ef,$entity->getEstado());
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
        
        
        return array(
            'entities' => $entities,
            'isContacto'    =>$isContacto,
            'isCoordinacion'    =>$isCoordinacion,
            'isAlumno'      =>  $isAlumno,
            'period_form'=>$periodo_form->createView(),
            'anterior'=>$anterior,
            'siguiente'=>$siguiente
        );
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
        $entity  = new Practica();
        $form = $this->createForm(new PracticaType(), $entity);
        $form->submit($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
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
     * @Route("/new/{step}/{idOrganizacion}/{idExterno}", name="practicas_new")
     */
    public function newAction($step = 1,$idOrganizacion = null,$idExterno = null)
    {
        $pm = $this->get('permission.manager');
        $user = $pm->getUser();
        
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();
        
        if($step == 1)
        {
            $organizacion = new Organizacion();
            $organizacion_form   = $this->createForm(new OrganizacionType(), $organizacion);
            
            $organizacionAlias = new OrganizacionAlias();
            $organizacionAlias_form = $this->createForm(new OrganizacionAliasType(), $organizacionAlias);
            
            return $this->render(
                'pDevPracticasBundle:Practica:new1.html.twig',
                array('organizacion_form' => $organizacion_form->createView(),
                    'organizacionAlias_form' => $organizacionAlias_form->createView())
            );
        }
        elseif($request->isMethod('POST') and $step == 2)
        {
            $organizacion = new Organizacion();
            $organizacion_form   = $this->createForm(new OrganizacionType(), $organizacion);
            
            $organizacionAlias = new OrganizacionAlias();
            $organizacionAlias_form = $this->createForm(new OrganizacionAliasType(), $organizacionAlias);
            
            $organizacion_form->submit($request);
            $organizacionAlias_form->submit($request);

            if ($organizacion_form->isValid() and $organizacionAlias_form->isValid())
            {
                $organizacion_tmp = $em->getRepository('pDevPracticasBundle:Organizacion')->findOneByRut($organizacion->getRut());
                
                if($organizacion_tmp)
                {
                    $organizacion = $organizacion_tmp;
                    $organizacion_form   = $this->createForm(new OrganizacionType(), $organizacion_tmp);
                    $organizacion_form->submit($request);
                }
                else
                    $em->persist($organizacion);
                
                
                $organizacionAlias_tmp = $em->getRepository('pDevPracticasBundle:OrganizacionAlias')->findOneByNombre($organizacionAlias->getNombre());
                if($organizacionAlias_tmp)
                {
                    $organizacionAlias_tmp->setOrganizacion ($organizacion);  
                    $organizacionAlias = $organizacionAlias_tmp;
                }
                else
                {
                    $organizacionAlias->setOrganizacion($organizacion);
                    $em->persist($organizacionAlias);
                }
                
                $em->flush();
            
            
                $externo = new Contacto();
                $externo_form   = $this->createForm(new ContactoType(), $externo);

                return $this->render(
                    'pDevPracticasBundle:Practica:new2.html.twig',
                    array('externo_form' => $externo_form->createView(),
                        'idOrganizacion'  => $organizacionAlias->getId())
                );
            }
            
            return $this->render(
                'pDevPracticasBundle:Practica:new1.html.twig',
                array('organizacion_form' => $organizacion_form->createView())
            );            
            
        }
        elseif($request->isMethod('POST') and $step == 3 and $idOrganizacion)
        {
            $organizacionAlias = $em->getRepository('pDevPracticasBundle:OrganizacionAlias')->find($idOrganizacion);
            $organizacion = $em->getRepository('pDevPracticasBundle:Organizacion')->find($organizacionAlias->getOrganizacion()->getId());
            
            $externo = new Contacto();
            $externo_form   = $this->createForm(new ContactoType(), $externo);
            $externo_form->submit($request);

            if ($externo_form->isValid())
            {
                $externo_tmp = $em->getRepository('pDevPracticasBundle:Contacto')->findOneByRut($externo->getRut());
                if($externo_tmp)
                {
                    $externo_form   = $this->createForm(new ContactoType(), $externo_tmp);
                    $externo_form->submit($request);
                    $externo = $externo_tmp;
                }
                else
                    $em->persist($externo);
                $em->flush();
                            
                $practica = new Practica();
                $practica_form   = $this->createForm(new PracticaType(), $practica);

                return $this->render(
                    'pDevPracticasBundle:Practica:new3.html.twig',
                    array('practica_form' => $practica_form->createView(),
                        'idOrganizacion'  => $organizacionAlias->getId(),
                        'idExterno' => $externo->getId())
                );
            
            }
            
            return $this->render(
                    'pDevPracticasBundle:Practica:new2.html.twig',
                    array('externo_form' => $externo_form->createView(),
                        'idOrganizacion'  => $organizacionAlias->getId())
                );
        }
        
        elseif($request->isMethod('POST') and $step == 4 and $idOrganizacion and $idExterno)
        {
            $organizacionAlias = $em->getRepository('pDevPracticasBundle:OrganizacionAlias')->find($idOrganizacion);
            $organizacion = $em->getRepository('pDevPracticasBundle:Organizacion')->find($organizacionAlias->getOrganizacion()->getId());
            $contacto = $em->getRepository('pDevPracticasBundle:Contacto')->find($idExterno);
            
            $practica = new Practica();
            $practica_form   = $this->createForm(new PracticaType(), $practica);
            $practica_form->submit($request);

            if ($practica_form->isValid())
            {
                $practica->setOrganizacionAlias($organizacionAlias);
                $practica->setContacto($contacto);
                $practica->setCreador($user);
                $em->persist($practica);
                $em->flush();
                            
                return $this->redirect($this->generateUrl('practicas_show', array('id' => $practica->getId())));            
            }
            
            return $this->render(
                    'pDevPracticasBundle:Practica:new3.html.twig',
                    array('practica_form' => $practica_form->createView(),
                        'idOrganizacion'  => $organizacionAlias->getId(),
                        'idExterno' => $externo->getId())
                );
        }

        
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
            'isCoordinacion'=>$isCoordinacion,
            
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

            return $this->redirect($this->generateUrl('practicas_edit', array('id' => $id)));
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

                return $this->redirect($this->generateUrl('practicas_show', array('id' => $id)));
            }
        }
        
        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
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
    
    private function createPeriodForm($data = null,$tooltip='Periodo académico')
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
