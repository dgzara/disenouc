<?php

namespace pDev\PracticasBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Doctrine\Common\Collections\ArrayCollection;

use pDev\UserBundle\Entity\Alumno;
use pDev\PracticasBundle\Entity\AlumnoPracticante;
use pDev\PracticasBundle\Entity\Supervisor;
use pDev\PracticasBundle\Entity\Organizacion;
use pDev\PracticasBundle\Entity\OrganizacionAlias;
use pDev\PracticasBundle\Entity\ProyectoTask;
use pDev\PracticasBundle\Entity\EvaluacionSupervisor;
use pDev\PracticasBundle\Entity\EvaluacionProfesor;
use pDev\PracticasBundle\Entity\Criterio;
use pDev\PracticasBundle\Entity\Desafio;
use pDev\PracticasBundle\Entity\Proyecto;

use pDev\PracticasBundle\Form\AlumnoPracticanteType;
use pDev\PracticasBundle\Form\AlumnoType;
use pDev\PracticasBundle\Form\SupervisorType;
use pDev\PracticasBundle\Form\OrganizacionAliasType;
use pDev\PracticasBundle\Form\OrganizacionType;
use pDev\PracticasBundle\Form\ProyectoTaskType;
use pDev\PracticasBundle\Form\CriterioType;
use pDev\PracticasBundle\Form\EvaluacionSupervisorType;
use pDev\PracticasBundle\Form\EvaluacionProfesorType;
use pDev\PracticasBundle\Form\AlumnoPracticanteEstadoType;

/**
 * AlumnoPracticante controller.
 *
 * @Route("/practicas/alumno")
 */
class AlumnoPracticanteController extends Controller
{
    /**
     * Lists all AlumnoPracticante entities.
     *
     * @Route("/todas/{estado}/{periodo}/{page}/{orderBy}/{order}", name="practicas_alumno")
     * @Template()
     */
    public function indexAction($estado = 'todos', $periodo = null, $page = null, $orderBy = null, $order = null)
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
            
            return $this->redirect($this->generateUrl('practicas_alumno',array('estado'=>$estado,'periodo'=>$periodo)));
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
            return $this->redirect($this->generateUrl('practicas_alumno',array('estado'=>$estado,'periodo'=>$periodo,'page'=>1,'orderBy'=>'fechaInicio','order'=>'asc')));
        }
                
        if($orderBy!='tipo' and $orderBy!='organizacion' and $orderBy!='fechaInicio' and $orderBy!='alumno')
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
        $isAcademico = $pm->checkType("TYPE_ACADEMICO");
        
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('pDevPracticasBundle:AlumnoPracticante');
        $consulta = $repo->createQueryBuilder('p')
                    ->leftJoin('p.alumno','a')
                    ->leftJoin('p.supervisor','s')
                    ->leftJoin('p.organizacionAlias','oa')
                    //->leftJoin('oa.organizacion','o')
                    ->leftJoin('oa.practicas','pr')
                    ->leftJoin('pr.contacto','c')
                    ->leftJoin('p.profesor','prof')
                    ->where('p.fechaInicio >= :fecha1 and p.fechaInicio <= :fecha2');
        
        $practicantes = array();
        $alumno = null;
        
        if($isAlumno)
        {
            //planes de practica de alumno
            $alumno = $user->getPersona('TYPE_ALUMNO');
        }
        if(!$isCoordinacion)
        {
            $where = null;
            
            if($isAlumno)
            {
                //planes de practica de alumno
                $alumno = $user->getPersona('TYPE_ALUMNO');
                $where = 'a.id = :idAlumno';
                $consulta = $consulta->setParameter('idAlumno', $alumno->getId());
            }
            if($isSupervisor)
            {
                //planes de practica donde es supervisor
                $supervisor = $user->getPersona('TYPE_PRACTICAS_SUPERVISOR');
                if($where!==null)
                    $where .= ' or ';
                $where = 's.id = :idSupervisor';
                $consulta = $consulta->setParameter('idSupervisor', $supervisor->getId());
                
            }
            if($isContacto)
            {
                // de las organizaciones
                $contacto = $user->getPersona('TYPE_PRACTICAS_CONTACTO');
                if($where!==null)
                    $where .= ' or ';
                $where = 'c.id = :id';
                $consulta = $consulta->setParameter('id', $contacto->getId());
                
            }
            if($isAcademico)
            {
                // evaluador
                $profesor = $user->getPersona('TYPE_ACADEMICO');
                if($where!==null)
                    $where .= ' or ';
                $where = 'prof.id = :id';
                $consulta = $consulta->setParameter('id', $profesor->getId());               
                
            }
            
            $consulta = $consulta->andWhere($where); 
        }
        
        if($estado!=='todos')
        {
            $estado = 'estado.'.$estado;
            $consulta = $consulta->andWhere('p.estado = :estado'); 
            $consulta = $consulta->setParameter('estado', $estado);
            
        }
        
        $entities = $consulta->setParameter('fecha1', $fecha1)->setParameter('fecha2', $fecha2);
        $entities2 = $entities->getQuery()->getResult();
        $count = count($entities2);
        $anterior = $offset>0?$page-1:false;
        $siguiente = $page*$limit<$count?$page + 1:false;
        
        if($offset>$count or $page < 1)
        {
            throw $this->createNotFoundException();
        }
        
        if($orderBy==='p.organizacion')
        {
            $orderBy = 'oa.nombre';            
        }
        
        if($orderBy==='p.alumno')
        {
            $orderBy = 'a.apellidoPaterno';            
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
        
        $estados = array(
            array('nombre'=>'Pendiente','identificador'=>'pendiente'),
            array('nombre'=>'Enviada','identificador'=>'enviada'),
            array('nombre'=>'Aprobada','identificador'=>'aprobada'),
            array('nombre'=>'Rechazada','identificador'=>'rechazada'),
            array('nombre'=>'Aceptada por alumno','identificador'=>'aceptada.alumno'),
            array('nombre'=>'Aceptada por organización','identificador'=>'aceptada.supervisor'),
            array('nombre'=>'Aceptada por alumno y organización','identificador'=>'aceptada'),
            array('nombre'=>'Iniciada','identificador'=>'iniciada'),
            array('nombre'=>'Terminada','identificador'=>'terminada'),
            array('nombre'=>'Informe entregado','identificador'=>'informe'),
            array('nombre'=>'Evaluada','identificador'=>'evaluada'),
        );
                
        return array(
            'entities' => $entities,
            'isExterno'=> $isExterno,
            'idAlumno'  => $isAlumno?$alumno->getId():false,
            'isAlumno' => $isAlumno,
            'isSupervisor'=> $isSupervisor,            
            'period_form'=>$periodo_form->createView(),
            'anterior'=>$anterior,
            'siguiente'=>$siguiente,
            'estados' => $estados
        );
    }
    
    /**
     * Displays a form to create a new AlumnoPracticante entity.
     *
     * @Route("/new", name="practicas_alumno_new")
     * @Template()
     */
    public function newAction()
    {
        $pm = $this->get('permission.manager');
        $user = $pm->getUser();
        $alumno = $user->getPersona('TYPE_ALUMNO');
        
        if(!$alumno) {
            throw $this->createNotFoundException('Unable to find Alumno entity.');
        }
        
        // Generamos la lista de organizaciones
        $form = $this->createFormBuilder()
            ->add('organizacionAlias', 'entity', array(
                'class' => 'pDevPracticasBundle:OrganizacionAlias',
                'property' => 'nombre',
                'mapped' => false
            ))
            ->getForm();
        
        // Generamos el formulario nuevo
        $organizacionAlias = new OrganizacionAlias();
        $organizacion = new Organizacion();
        $supervisor = new Supervisor();
        
        $organizacionAlias_form = $this->createForm(new OrganizacionAliasType(), $organizacionAlias);
        $organizacion_form = $this->createForm(new OrganizacionType(), $organizacion);
        $supervisor_form = $this->createForm(new SupervisorType(), $supervisor);
        
        return array(
            'form' => $form->createView(),
            'organizacion_form' => $organizacion_form->createView(),
            'organizacionAlias_form' => $organizacionAlias_form->createView(),
            'supervisor_form' => $supervisor_form->createView(),
        );
    }
    
    /**
     * Creates a new AlumnoPracticante entity.
     *
     * @Route("/create", name="practicas_alumno_create")
     * @Route("/create/{id}", name="practicas_alumno_create_source")
     * @Method("POST")
     * @Template("pDevPracticasBundle:AlumnoPracticante:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $pm = $this->get('permission.manager');
        $em = $this->getDoctrine()->getManager();
        $user = $pm->getUser();
        $alumno = $user->getPersona('TYPE_ALUMNO');
        
        if(!$alumno) {
            throw $this->createNotFoundException('Unable to find Alumno entity.');
        }
        
        // Generamos la lista de organizaciones
        $form = $this->createFormBuilder()
            ->add('organizacionAlias', 'entity', array(
                'class' => 'pDevPracticasBundle:OrganizacionAlias',
                'property' => 'nombre',
                'mapped' => false
            ))
            ->getForm();
            
        // Generamos el formulario nuevo
        $organizacionAlias = new OrganizacionAlias();
        $organizacion = new Organizacion();
        $supervisor = new Supervisor();
        
        $organizacionAlias_form = $this->createForm(new OrganizacionAliasType(), $organizacionAlias);
        $organizacion_form = $this->createForm(new OrganizacionType(), $organizacion);
        $supervisor_form = $this->createForm(new SupervisorType(), $supervisor);
        
        if($request->isMethod('POST'))
        {
            $form->submit($request);
            $organizacionAlias_form->submit($request);
            $organizacion_form->submit($request);
            $supervisor_form->submit($request);
            
            if($form->isValid())
            {
                $organizacionAlias = $form->get('organizacionAlias')->getData();
                return $this->redirect ($this->generateUrl ('practicas_alumno_new_datos',array('idOrganizacionAlias' => $organizacionAlias->getId())));
            }
            elseif($organizacion_form->isValid() and $organizacionAlias_form->isValid() and $supervisor_form->isValid())
            {
                $organizacionAlias->setOrganizacion($organizacion);
                $supervisor->addOrganizacion($organizacion);
                
                $em->persist($organizacion);
                $em->persist($supervisor);
                $em->persist($organizacionAlias);
                
                $em->flush();
                
                return $this->redirect ($this->generateUrl ('practicas_alumno_new_datos',array('idOrganizacionAlias' => $organizacionAlias->getId())));
            }
        }
          
        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'organizacion_form' => $organizacion_form->createView(),
            'organizacionAlias_form' => $organizacionAlias_form->createView(),
            'supervisor_form' => $supervisor_form->createView(),
        );
    }
    
    /**
     * Displays a form to create a new AlumnoPracticante entity.
     *
     * @Route("/new/datos/organizacion/{idOrganizacionAlias}", name="practicas_alumno_new_datos")
     * @Route("/new/datos/practica/{id}", name="practicas_alumno_new_datos_source")
     * @Template()
     */
    public function datosAction($id = null, $idOrganizacionAlias = null)
    {
        $pm = $this->get('permission.manager');
        $em = $this->getDoctrine()->getManager();
        $user = $pm->getUser();
        $alumno = $user->getPersona('TYPE_ALUMNO');
        
        if(!$alumno) {
            throw $this->createNotFoundException('Unable to find Alumno entity.');
        }
        
        $entity = new AlumnoPracticante();
        $entity->setAlumno($alumno);
        $entity->addDesafio(new Desafio());
        $entity->addDesafio(new Desafio());
        $entity->addDesafio(new Desafio());
        $entity->addDesafio(new Desafio());
        $entity->addDesafio(new Desafio());
        $entity->addProyecto(new Proyecto());
        $entity->addProyecto(new Proyecto());
        $entity->addProyecto(new Proyecto());  
        
        // Si hay una practica asociada, la adjuntamos
        if($id)
        {
            $practica = $em->getRepository('pDevPracticasBundle:Practica')->find($id);

            if (!$practica) {
                throw $this->createNotFoundException('Unable to find Practica entity.');
            }
            
            $entity->setPractica($practica);
            $entity->setOrganizacionAlias($practica->getOrganizacionAlias());
            $entity->setSupervisor($practica->getSupervisor());
            $entity->setComoContacto("Ofertas publicadas en este sitio");
            $entity->setFechaInicio($practica->getFechaInicio());
            $entity->setDuracionCantidad($practica->getDuracionCantidad());
            $entity->setDuracionUnidad($practica->getDuracionUnidad());
            $entity->setTipo($practica->getTipo());
        }
        elseif($idOrganizacionAlias)
        {
            $organizacionAlias = $em->getRepository('pDevPracticasBundle:OrganizacionAlias')->find($idOrganizacionAlias);
            
            if (!$organizacionAlias) {
                throw $this->createNotFoundException('Unable to find organizacionAlias entity.');
            }
            
            // Buscamos al supervisor
            $organizacion = $organizacionAlias->getOrganizacion();
            $supervisor = $organizacion->getSupervisores()->last();
            
            $entity->setOrganizacionAlias($organizacionAlias);
            
            if($supervisor)
                $entity->setSupervisor($supervisor);
        }
        
        $form = $this->createForm(new AlumnoPracticanteType(), $entity);
        
        // Eliminamos los datos
        $form->remove('organizacionAlias');
        $form->remove('contacto');
        $form->remove('supervisor');
            
        // Si esta asociada a una practica, borramos los campos de organizacion y contacto
        if($id){
            $form->remove('comoContacto');
            $form->remove('fechaInicio');
            $form->remove('fechaTermino');
            $form->remove('duracionCantidad');
            $form->remove('duracionUnidad');
            $form->remove('tipo');
        }
          
        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }
        
    /**
     * Creates a new AlumnoPracticante entity.
     *
     * @Route("/datos/create/organizacion/{idOrganizacionAlias}", name="practicas_alumno_create_datos")
     * @Route("/datos/create/practica/{id}", name="practicas_alumno_create_datos_source")
     * @Method("POST")
     * @Template("pDevPracticasBundle:AlumnoPracticante:datos.html.twig")
     */
    public function datosCreateAction(Request $request, $id = null)
    {
        $pm = $this->get('permission.manager');
        $user = $pm->getUser();
        $alumno = $user->getPersona('TYPE_ALUMNO');
        
        if(!$alumno) {
            throw $this->createNotFoundException('Unable to find Alumno entity.');
        }
        
        $entity = new AlumnoPracticante();
        $entity->setAlumno($alumno);
        $entity->addDesafio(new Desafio());
        $entity->addDesafio(new Desafio());
        $entity->addDesafio(new Desafio());
        $entity->addDesafio(new Desafio());
        $entity->addDesafio(new Desafio());
        $entity->addProyecto(new Proyecto());
        $entity->addProyecto(new Proyecto());
        $entity->addProyecto(new Proyecto());  
        
        // Si hay una practica asociada, la adjuntamos
        if($id)
        {
            $em = $this->getDoctrine()->getManager();
            $practica = $em->getRepository('pDevPracticasBundle:Practica')->find($id);

            if (!$practica) {
                throw $this->createNotFoundException('Unable to find Practica entity.');
            }
            
            $entity->setPractica($practica);
            $entity->setOrganizacionAlias($practica->getOrganizacionAlias());
            $entity->setSupervisor($practica->getSupervisor());
            $entity->setComoContacto("Ofertas publicadas en este sitio");
            $entity->setFechaInicio($practica->getFechaInicio());
            $entity->setDuracionCantidad($practica->getDuracionCantidad());
            $entity->setDuracionUnidad($practica->getDuracionUnidad());
            $entity->setTipo($practica->getTipo());
        }
        elseif($idOrganizacionAlias)
        {
            $organizacionAlias = $em->getRepository('pDevPracticasBundle:OrganizacionAlias')->find($idOrganizacionAlias);
            
            if (!$organizacionAlias) {
                throw $this->createNotFoundException('Unable to find organizacionAlias entity.');
            }
            
            // Buscamos al supervisor
            $organizacion = $organizacionAlias->getOrganizacion();
            $supervisor = $organizacion->getSupervisores()->last();
            
            $entity->setOrganizacionAlias($organizacionAlias);
            
            if($supervisor)
                $entity->setSupervisor($supervisor);
        }
        
        $form = $this->createForm(new AlumnoPracticanteType(), $entity);

        // Eliminamos los datos
        $form->remove('organizacionAlias');
        $form->remove('contacto');
        $form->remove('supervisor');
            
        // Si esta asociada a una practica, borramos los campos de organizacion y contacto
        if($id){
            $form->remove('comoContacto');
            $form->remove('fechaInicio');
            $form->remove('fechaTermino');
            $form->remove('duracionCantidad');
            $form->remove('duracionUnidad');
            $form->remove('tipo');
        }
        
        $form->submit($request);

        if ($form->isValid()) 
        {
            $em = $this->getDoctrine()->getManager();
            
            // Generamos las fechas
            $tomorrow = new \DateTime();
            $tomorrow->modify('+1 day');
            
            // Creamos las tareas
            foreach($entity->getProyectos() as $proyecto)
            {
                $tarea = new ProyectoTask();
                $tarea->setFechaInicio(new \DateTime());
                $tarea->setFechaTermino($tomorrow);
                $tarea->setNombre('Tarea 1');
                $tarea->setProyecto($proyecto);
                $proyecto->addTarea($tarea);
                $em->persist($tarea);
            }
            
            // Guardamos
            $em->persist($entity);
            $em->flush();
            
            return $this->redirect($this->generateUrl('practicas_alumno_gantt', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new AlumnoPracticante entity.
     *
     * @Route("/{id}/gantt", name="practicas_alumno_gantt")
     * @Method("GET")
     * @Template()
     */
    public function ganttAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('pDevPracticasBundle:AlumnoPracticante')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AlumnoPracticante entity.');
        }

        return array(
            'entity' => $entity,
        );
    }
    
    /**
     * Creates a new AlumnoPracticante entity.
     *
     * @Route("/{id}/gantt/create", name="practicas_alumno_gantt_create")
     * @Method("GET")
     * @Template("pDevPracticasBundle:AlumnoPracticante:confirm.html.twig")
     */
    public function ganttCreateAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('pDevPracticasBundle:AlumnoPracticante')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AlumnoPracticante entity.');
        }
        
        $confirmForm = $this->createConfirmForm($id);

        return array(
            'entity' => $entity,
            'form'  => $confirmForm->createView()
        );
    }
    
    /**
     * Send to confirm one AlumnoPracticante entity.
     *
     * @Route("/{id}/confirm", name="practicas_alumno_confirm")
     * @Method("POST")
     * @Template("pDevPracticasBundle:AlumnoPracticante:confirm.html.twig")
     */
    public function confirmAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('pDevPracticasBundle:AlumnoPracticante')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AlumnoPracticante entity.');
        }

        $confirmForm = $this->createConfirmForm($id);
        $confirmForm->submit($request);

        if ($confirmForm->isValid()) {
            $entity->setEstado(AlumnoPracticante::ESTADO_ENVIADA);
            $em->persist($entity);
            $em->flush();
            
            return $this->redirect($this->generateUrl('practicas_alumno'));
        }

        return array(
            'entity' => $entity,
            'form'  => $confirmForm->createView()
        );
    }
    
    /**
     * Finds and displays a AlumnoPracticante entity.
     *
     * @Route("/{id}/gantt/json", name="practicas_alumno_gantt_json", options={"expose"=true})
     * @Method("GET")     
     */
    public function ganttDataAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('pDevPracticasBundle:AlumnoPracticante')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AlumnoPracticante entity.');
        }

        $pjson = array();
        $count = 1;

        foreach($entity->getProyectos() as $proyecto)
        {
            $series = array();

            foreach($proyecto->getTareas() as $tarea)
            {
                $start_date = $tarea->getFechaInicio()?$tarea->getFechaInicio():new \DateTime();                    
                $start = date_format($start_date,'Y,m,d');
                $end_date = $tarea->getFechaTermino()?$tarea->getFechaTermino():new \DateTime();                    
                $end = date_format($end_date,'Y,m,d');

                $series[] = array(  
                    'name'  =>  $tarea->getNombre(),
                    'start' =>  'new Date('.$start.')',
                    'end'   =>  'new Date('.$end.')',
                    'idTarea'   => $tarea->getId()
                );
            }

            $pjson[] = array(
                'id'=> $count++,
                'name'  => $proyecto->getNombre(),
                'idProyecto'    => $proyecto->getId(),
                'series'    => $series
            );
        }
        
        $response = new Response(json_encode($pjson));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Displays a form to edit an existing AlumnoPracticante entity.
     *
     * @Route("/{id}/evaluar", name="practicas_alumno_evaluar")
     */
    public function evaluarAction($id)
    {
        $pm = $this->get('permission.manager');
        $user = $pm->getUser();
        $em = $this->getDoctrine()->getManager();

        $practicante = $em->getRepository('pDevPracticasBundle:AlumnoPracticante')->find($id);

        if (!$practicante) {
            throw $this->createNotFoundException('Unable to find AlumnoPracticante entity.');
        }
        
        if($user->hasPersona('TYPE_ACADEMICO'))
        {
            if($practicante->getProfesor()->getProfesor()->getId()==$user->getPersona('TYPE_ACADEMICO')->getId())
                return $this->redirect ($this->generateUrl ('practicas_alumno_evaluar_profesor',array('id'=>$id)));
        }
        
        if($user->hasPersona('TYPE_PRACTICAS_SUPERVISOR'))
        {
            if($practicante->getSupervisor()->getId()==$user->getPersona('TYPE_PRACTICAS_SUPERVISOR')->getId())
                return $this->redirect ($this->generateUrl ('practicas_alumno_evaluar_supervisor',array('id'=>$id)));
        }
        
        $pm->throwForbidden();
    }
    
    /**
     * Displays a form to edit an existing AlumnoPracticante entity.
     *
     * @Route("/{id}/evaluar/supervisor", name="practicas_alumno_evaluar_supervisor")
     * @Template()
     */
    public function evaluarSupervisorAction($id)
    {
        $pm = $this->get('permission.manager');
        $user = $pm->getUser();
        
        $em = $this->getDoctrine()->getManager();

        $practicante = $em->getRepository('pDevPracticasBundle:AlumnoPracticante')->find($id);

        if (!$practicante) {
            throw $this->createNotFoundException('Unable to find AlumnoPracticante entity.');
        }
        
        $evaluacionSupervisor = $practicante->getSupervisorEvaluacion();
        if(!$evaluacionSupervisor)
        {
            $evaluacionSupervisor = new EvaluacionSupervisor();
            $evaluacionSupervisor->setSupervisor($practicante->getSupervisor());
            $em->persist($evaluacionSupervisor);
            $practicante->setSupervisorEvaluacion($evaluacionSupervisor);

            $criterios = $em->getRepository('pDevPracticasBundle:CriterioTipo')->findBy(array('tipoPractica'=>$practicante->getTipo(),'tipoEvaluador'=>'Supervisor'));
            
            foreach($criterios as $criterioTipo)
            {
                $criterio = new Criterio();
                $criterio->setCriterioTipo($criterioTipo);
                $criterio->setEvaluacion($evaluacionSupervisor);
                $em->persist($criterio);
                $evaluacionSupervisor->addCriterio($criterio);
            }

            $em->flush();
        }
            
        $editForm = $this->createForm(new EvaluacionSupervisorType(), $evaluacionSupervisor);
        
        $request = $this->getRequest();
        
        if($request->isMethod('PUT'))
        {
            $editForm->submit($request);
            if($editForm->isValid())
            {
                $evaluacionSupervisor->setNota($evaluacionSupervisor->calculaNota());                
                $em->persist($evaluacionSupervisor);
                $em->flush();
                
                return $this->redirect($this->generateUrl('practicas_alumno'));
            }                
        }

        return array(
            'idPracticante' => $practicante->getId(),
            'edit_form'     => $editForm->createView(),
        );
    }
    
    /**
     * Displays a form to edit an existing AlumnoPracticante entity.
     *
     * @Route("/{id}/evaluar/profesor", name="practicas_alumno_evaluar_profesor")
     * @Template()
     */
    public function evaluarProfesorAction($id)
    {
        $pm = $this->get('permission.manager');
        $user = $pm->getUser();
        
        $em = $this->getDoctrine()->getManager();

        $practicante = $em->getRepository('pDevPracticasBundle:AlumnoPracticante')->find($id);

        if (!$practicante) {
            throw $this->createNotFoundException('Unable to find AlumnoPracticante entity.');
        }
        
        $evaluacionProfesor = $practicante->getProfesorEvaluacion();
        if(!$evaluacionProfesor)
        {
            throw $this->createNotFoundException('Unable to find evaluacionProfesor entity.');
        }
            
        $editForm = $this->createForm(new EvaluacionProfesorType(), $evaluacionProfesor);
        $request = $this->getRequest();
        
        if($request->isMethod('PUT'))
        {
            $editForm->submit($request);
            if($editForm->isValid())
            {
                $evaluacionProfesor->setNota($evaluacionProfesor->calculaNota());
                $evaluacionProfesor->setNotaFinal($evaluacionProfesor->calculaNotaFinal());
                $em->persist($evaluacionProfesor);
                $em->flush();
                
                return $this->redirect($this->generateUrl('practicas_alumno'));
            }
        }
        
        return array(
            'idPracticante'      => $practicante->getId(),
            'edit_form'   => $editForm->createView(),
        );
    }
    
    /**
     * Displays a form to edit an existing Practica entity.
     *
     * @Route("/{id}/estado", name="practicas_alumno_estado")
     * @Template()
     */
    public function estadoAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('pDevPracticasBundle:AlumnoPracticante')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AlumnoPracticante entity.');
        }

        $editForm = $this->createForm(new AlumnoPracticanteEstadoType(), $entity);
        
        $request = $this->getRequest();
        if($request->isMethod('POST'))
        {
            $editForm->submit($request);

            if ($editForm->isValid()) {
                $em->persist($entity);
                $em->flush();

                return $this->redirect($this->generateUrl('practicas_alumno_show', array('id' => $id)));
            }
        }
        
        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }
    
    /**
     * Displays a form to edit an existing Practica entity.
     *
     * @Route("/{id}/aceptar", name="practicas_alumno_aceptar")
     * @Template()
     */
    public function aceptarAction(Request $request, $id)
    {
        $pm = $this->get('permission.manager');
        $user = $pm->getUser();
        
        $isAlumno = false;
        $isSupervisor = false;
        $isContacto = false;
        $isAcademico = false;
        $isCoordinacion = $pm->isGranted("ROLE_ADMIN","SITE_PRACTICAS");
        
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('pDevPracticasBundle:AlumnoPracticante')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AlumnoPracticante entity.');
        }
        
        // Verificamos que es el alumno, supervisor, contacto relacionado
        if($pm->checkType("TYPE_ALUMNO") and $entity->hasAlumno($user->getPersona('TYPE_ALUMNO')))
            $isAlumno = true;
        
        // Verificamos que es el alumno, supervisor, contacto relacionado
        if($pm->checkType("TYPE_PRACTICAS_SUPERVISOR") and $entity->hasSupervisor($user->getPersona('TYPE_PRACTICAS_SUPERVISOR')))
            $isSupervisor = true;
            
        // Verificamos que es el alumno, supervisor, contacto relacionado
        if($pm->checkType("TYPE_PRACTICAS_CONTACTO") and $entity->hasContacto($user->getPersona('TYPE_PRACTICAS_CONTACTO')))
            $isContacto = true;
            
        // Verificamos que es el alumno, supervisor, contacto relacionado
        if($pm->checkType("TYPE_ACADEMICO") and $entity->hasAcademico($user->getPersona('TYPE_ACADEMICO')))
            $isAcademico = true;

        $aceptaForm = $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;

        if($request->isMethod('POST'))
        {
            $aceptaForm->submit($request);

            if ($aceptaForm->isValid()) 
            {
                $estado = $entity->getEstado();
                
                if($isCoordinacion and $estado === AlumnoPracticante::ESTADO_ENVIADA)
                {
                    $estado = AlumnoPracticante::ESTADO_APROBADA;
                }
                elseif($estado === AlumnoPracticante::ESTADO_APROBADA)
                {
                    if($isAlumno)
                        $estado = AlumnoPracticante::ESTADO_ACEPTADA_ALUMNO;
                    elseif($isContacto or $isSupervisor)
                        $estado = AlumnoPracticante::ESTADO_ACEPTADA_SUPERVISOR;
                }
                elseif($estado === AlumnoPracticante::ESTADO_ACEPTADA_ALUMNO or $estado === AlumnoPracticante::ESTADO_ACEPTADA_SUPERVISOR)
                    $estado = AlumnoPracticante::ESTADO_ACEPTADA;
                elseif($isCoordinacion and $estado = AlumnoPracticante::ESTADO_ACEPTADA)
                    $estado = AlumnoPracticante::ESTADO_INICIADA;
                
                $entity->setEstado($estado);
                $em->persist($entity);
                $em->flush();
                
                // Devuelve la ruta
                $array = array('redirect' => $this->generateUrl('practicas_alumno_show', array('id' => $id))); // data to return via JSON
                $response = new Response(json_encode($array));
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
        }
        
        return array(
            'entity' => $entity,
            'form'   => $aceptaForm->createView(),
        );
    }
    
    /**
     * Finds and displays a AlumnoPracticante entity.
     *
     * @Route("/{id}/show", name="practicas_alumno_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $pm = $this->get('permission.manager');
        $user = $pm->getUser();
        
        $isExterno = $user->getExternal();
        $isAlumno = false;
        $isSupervisor = false;
        $isContacto = false;
        $isAcademico = false;
        $isCoordinacion = $pm->isGranted("ROLE_ADMIN","SITE_PRACTICAS");
        
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('pDevPracticasBundle:AlumnoPracticante')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AlumnoPracticante entity.');
        }
        
        // Verificamos que es el alumno, supervisor, contacto relacionado
        if($pm->checkType("TYPE_ALUMNO") and $entity->hasAlumno($user->getPersona('TYPE_ALUMNO')))
            $isAlumno = true;
        
        // Verificamos que es el alumno, supervisor, contacto relacionado
        if($pm->checkType("TYPE_PRACTICAS_SUPERVISOR") and $entity->hasSupervisor($user->getPersona('TYPE_PRACTICAS_SUPERVISOR')))
            $isSupervisor = true;
            
        // Verificamos que es el alumno, supervisor, contacto relacionado
        if($pm->checkType("TYPE_PRACTICAS_CONTACTO") and $entity->hasContacto($user->getPersona('TYPE_PRACTICAS_CONTACTO')))
            $isContacto = true;
            
        // Verificamos que es el alumno, supervisor, contacto relacionado
        if($pm->checkType("TYPE_ACADEMICO") and $entity->hasAcademico($user->getPersona('TYPE_ACADEMICO')))
            $isAcademico = true;
            
        return array(
            'entity'        => $entity,
            'isCoordinacion'=> $isCoordinacion,
            'isAcademico'   => $isAcademico,
            'isAlumno'      => $isAlumno,
            'isContacto'    => $isContacto,
            'isSupervisor'  => $isSupervisor
        );
    }
    
    /**
     * Displays a form to edit an existing AlumnoPracticante entity.
     *
     * @Route("/{id}/edit", name="practicas_alumno_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('pDevPracticasBundle:AlumnoPracticante')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AlumnoPracticante entity.');
        }

        $editForm = $this->createForm(new AlumnoPracticanteType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing AlumnoPracticante entity.
     *
     * @Route("/{id}", name="practicas_alumno_update")
     * @Method("PUT")
     * @Template("pDevPracticasBundle:AlumnoPracticante:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('pDevPracticasBundle:AlumnoPracticante')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AlumnoPracticante entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new AlumnoPracticanteType(), $entity);
        $editForm->submit($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('practicas_alumno_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    
    /**
     * Deletes a AlumnoPracticante entity.
     *
     * @Route("/{id}", name="practicas_alumno_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->submit($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('pDevPracticasBundle:AlumnoPracticante')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find AlumnoPracticante entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('practicas_alumno'));
    }
    
    /**
     * Displays a form to create a new Organizacion entity.
     *
     * @Route("/find", name="practicas_alumno_find")
     * @Method("GET")
     */
    public function searchAction()
    {
        $data = strtolower($this->get('request')->query->get('term'));
        $em = $this->getDoctrine()->getManager();

        $qb = $em->getRepository('pDevUserBundle:Alumno')->createQueryBuilder('p');
        $entities = $qb ->where('p.numeroAlumno like :numal')
                        ->setParameter('numal','%'.$data.'%')
                        ->getQuery()
                        ->getResult();
        
        $return = array();

        foreach ($entities as $alumno)
        {
            $return[] = array(
                'label'=> $alumno->getNumeroAlumno().' '.$alumno->getNombreCompleto(),
                'value'=> $alumno->getNumeroAlumno()
                );
        }
        
        $response = new Response(json_encode($return));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
    
    /**
     * Creates a form to delete a AlumnoPracticante entity by id.
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
    
    /**
     * Creates a form to confirm a AlumnoPracticante entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createConfirmForm($id)
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

