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
    public function indexAction($estado = 'todos',$periodo = null,$page = null,$orderBy = null,$order = null)
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
                $consulta = $consulta->setParameter('idAlumno',$alumno->getId());
            }

            if($isSupervisor)
            {
                //planes de practica donde es supervisor
                $supervisor = $user->getPersona('TYPE_PRACTICAS_SUPERVISOR');
                if($where!==null)
                    $where .= ' or ';
                $where = 's.id = :idSupervisor';
                $consulta = $consulta->setParameter('idSupervisor',$supervisor->getId());
                
            }

            if($isContacto)
            {
                // de las organizaciones
                $contacto = $user->getPersona('TYPE_PRACTICAS_CONTACTO');
                if($where!==null)
                    $where .= ' or ';
                $where = 'c.id = :id';
                $consulta = $consulta->setParameter('id',$contacto->getId());
                
            }



            if($isAcademico)
            {
                // evaluador
                $profesor = $user->getPersona('TYPE_ACADEMICO');
                if($where!==null)
                    $where .= ' or ';
                $where = 'prof.id = :id';
                $consulta = $consulta->setParameter('id',$profesor->getId());               
                
            }
            
            $consulta = $consulta->andWhere($where); 
        }
        
        if($estado!=='todos')
        {
            $estado = 'estado.'.$estado;
            $consulta = $consulta->andWhere('p.estado = :estado'); 
            $consulta = $consulta->setParameter('estado',$estado);
            
        }
        
        $entities = $consulta->setParameter('fecha1', $fecha1)
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

            $excelService->excelObj->getActiveSheet()->setCellValueByColumnAndRow($ec,$ef,'ALUMNO');
            $ec++;
            $excelService->excelObj->getActiveSheet()->setCellValueByColumnAndRow($ec,$ef,'NUMERO ALUMNO');
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
                $excelService->excelObj->getActiveSheet()->setCellValueByColumnAndRow($ec,$ef,$entity->getAlumno()->getNombreCompleto());
                $ec++;
                $excelService->excelObj->getActiveSheet()->setCellValueByColumnAndRow($ec,$ef,$entity->getAlumno()->getNumeroAlumno());
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
            'isSupervisor'=> $isSupervisor,            
            'period_form'=>$periodo_form->createView(),
            'anterior'=>$anterior,
            'siguiente'=>$siguiente,
            'estados' => $estados
            
        );
    }
    
    /**
     * Creates a new AlumnoPracticante entity.
     *
     * @Route("/", name="practicas_alumno_create")
     * @Method("POST")
     * @Template("pDevPracticasBundle:AlumnoPracticante:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new AlumnoPracticante();
        $form = $this->createForm(new AlumnoPracticanteType(), $entity);
        $form->submit($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('practicas_alumno_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new AlumnoPracticante entity.
     *
     * @Route("/wizard/{idPracticante}/{step}/{idAlumno}/{idOrganizacionAlias}/{idSupervisor}", name="practicas_alumno_wizard")
     */
    public function newAction($idPracticante,$step=1,$idAlumno=null,$idOrganizacionAlias=null,$idSupervisor=null)
    {
        $pm = $this->get('permission.manager');
        $user = $pm->getUser();
        $alumno = $user->getPersona('TYPE_ALUMNO');
        if($alumno)
            $idAlumno = $alumno->getId();
        else
            $alumno = new Alumno();
        
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();
        
        $practicante = null;
        if($idPracticante !== 'new')
        {
            $practicante = $em->getRepository('pDevPracticasBundle:AlumnoPracticante')->find($idPracticante);
            if($practicante and $practicante->getAlumno()->getId() != $idAlumno)
            {
                $practicante = null;
            }   
            elseif($practicante)
            {
                if(!$idOrganizacionAlias)
                    $idOrganizacionAlias = $practicante->getOrganizacionAlias()->getId();
                if(!$idSupervisor)
                    $idSupervisor = $practicante->getSupervisor()->getId();
            }
        }
        
        if(!$practicante)
        {
            $practicante = new AlumnoPracticante();
            $practicante->addDesafio(new Desafio());
            $practicante->addDesafio(new Desafio());
            $practicante->addDesafio(new Desafio());
            $practicante->addDesafio(new Desafio());
            $practicante->addDesafio(new Desafio());
            $practicante->addProyecto(new Proyecto());
            $practicante->addProyecto(new Proyecto());
            $practicante->addProyecto(new Proyecto());
        }   
        
        
        $organizacionAlias = null;
        if($idOrganizacionAlias)
            $organizacionAlias = $em->getRepository('pDevPracticasBundle:OrganizacionAlias')->find($idOrganizacionAlias);
        if(!$organizacionAlias)
            $organizacionAlias = new OrganizacionAlias();
            
        $organizacion = null;
        if($organizacionAlias->getOrganizacion())
            $organizacion = $em->getRepository('pDevPracticasBundle:Organizacion')->find($organizacionAlias->getOrganizacion()->getId());
        if(!$organizacion)
            $organizacion = new Organizacion();
        
        
        $supervisor = null;
        if($idSupervisor)
            $supervisor = $em->getRepository('pDevPracticasBundle:Supervisor')->find($idSupervisor);
        if(!$supervisor)
            $supervisor = new Supervisor();
            
        // datos de alumno
        if($step == 1)
        {
            $alumno_form   = $this->createForm(new AlumnoType(), $alumno);
            
            return $this->render(
                'pDevPracticasBundle:AlumnoPracticante:new1.html.twig',
                array('alumno_form' => $alumno_form->createView())
            );
        }
        // datos de organizacion
        else if($step == 2)
        {
            
            $alumno_form   = $this->createForm(new AlumnoType(), $alumno);
                
            if($request->isMethod('POST'))
            {
                $alumno_form->submit($request);
                if($alumno_form->isValid())
                {
                    $alumno2 = $em->getRepository('pDevUserBundle:Alumno')->findOneByNumeroAlumno($alumno->getNumeroAlumno());
                    if(!$alumno2)
                    {
                        $alumno2 = $alumno;
                    }
            
                    $alumno2->setRut($user->getRut());
                    $alumno2->setUsuario($user);
                    $em->persist($alumno2);
                    $em->flush();
                    
                    $idAlumno = $alumno2->getId();
                    
                    return $this->redirect ($this->generateUrl ('practicas_alumno_wizard',array('step'=>2,'idAlumno'=>$idAlumno,'idPracticante'=>$idPracticante)));
                }
            }
            
            if(!$idAlumno)
                return $this->redirect ($this->generateUrl ('practicas_alumno_wizard',array('step'=>1,'idPracticante'=>$idPracticante)));
                        
            $organizacionAlias_form = $this->createForm(new OrganizacionAliasType(), $organizacionAlias);
            $organizacion_form   = $this->createForm(new OrganizacionType(), $organizacion);
            
            return $this->render(
                'pDevPracticasBundle:AlumnoPracticante:new2.html.twig',
                array('organizacion_form' => $organizacion_form->createView(),
                    'organizacionAlias_form' => $organizacionAlias_form->createView(),
                    'idAlumno'  => $idAlumno,
                    'idPracticante'=>$idPracticante
                    )
            );
        }
        
        if(!$idAlumno)
            return $this->redirect ($this->generateUrl ('practicas_alumno_wizard',array('step'=>1,'idPracticante'=>$idPracticante)));
        
        // datos supervisor
        if($step == 3)
        {
            if($request->isMethod('POST'))
            {
                $organizacionAlias_form = $this->createForm(new OrganizacionAliasType(), $organizacionAlias);
                $organizacion_form   = $this->createForm(new OrganizacionType(), $organizacion);
                
                $organizacionAlias_form->submit($request);
                $organizacion_form->submit($request);
                if($organizacion_form->isValid() and $organizacionAlias_form->isValid())
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
                    
                    $idOrganizacionAlias = $organizacionAlias->getId();
                    
                    return $this->redirect ($this->generateUrl ('practicas_alumno_wizard',array('step'=>3,'idAlumno'=>$idAlumno,'idOrganizacionAlias'=>$idOrganizacionAlias,'idPracticante'=>$idPracticante)));
                }
            }
            
            if(!$idOrganizacionAlias)
                return $this->redirect ($this->generateUrl ('practicas_alumno_wizard',array('step'=>2,'idPracticante'=>$idPracticante)));
            
            $supervisor_form = $this->createForm(new SupervisorType(),$supervisor);
            
            return $this->render(
                'pDevPracticasBundle:AlumnoPracticante:new3.html.twig',
                array('supervisor_form' => $supervisor_form->createView(),
                    'idOrganizacionAlias' => $idOrganizacionAlias,
                    'idAlumno'  => $idAlumno,
                    'idPracticante'=>$idPracticante)
            );
        }
        
        if(!$idOrganizacionAlias)
                return $this->redirect ($this->generateUrl ('practicas_alumno_wizard',array('step'=>2,'idPracticante'=>$idPracticante)));
        // detalles de practica
        if($step == 4)
        {
            
            
            if($request->isMethod('POST'))
            {
                $supervisor_form = $this->createForm(new SupervisorType(),$supervisor);
                
                $supervisor_form->submit($request);
                
                if($supervisor_form->isValid())
                {
                    $supervisor_tmp = $em->getRepository('pDevPracticasBundle:Supervisor')->findOneByRut($supervisor->getRut());
                    if($supervisor_tmp)
                    {
                        $supervisor = $supervisor_tmp;
                        $supervisor_form = $this->createForm(new SupervisorType(),$supervisor);
                        $supervisor_form->submit($request);
                    }
                    
                    $em->persist($supervisor);
                    $em->flush();
                    
                    $idSupervisor = $supervisor->getId();
                    return $this->redirect ($this->generateUrl ('practicas_alumno_wizard',array('step'=>4,'idAlumno'=>$idAlumno,'idOrganizacionAlias'=>$idOrganizacionAlias,'idSupervisor'=>$idSupervisor,'idPracticante'=>$idPracticante)));
                }
            }
            
            if(!$idSupervisor)
                return $this->redirect ($this->generateUrl ('practicas_alumno_wizard',array('step'=>3,'idAlumno'=>$idAlumno,'idPracticante'=>$idPracticante)));
            
            $practicante_form = $this->createForm(new AlumnoPracticanteType(), $practicante);
            
            return $this->render(
                'pDevPracticasBundle:AlumnoPracticante:new4.html.twig',
                array('practicante_form' => $practicante_form->createView(),
                    'idOrganizacionAlias' => $organizacionAlias->getId(),
                    'idSupervisor' => $supervisor->getId(),
                    'idAlumno'  => $idAlumno,
                    'idPracticante'=>$idPracticante)
            );
        }
        
        if(!$idSupervisor)
                return $this->redirect ($this->generateUrl ('practicas_alumno_wizard',array('step'=>3,'idAlumno'=>$idAlumno,'idPracticante'=>$idPracticante)));
        
        //carta gantt
        if($step == 5)
        {
            if($request->isMethod('POST'))
            {
                $practicante_form = $this->createForm(new AlumnoPracticanteType(),$practicante);
                
                $originalProyectos = new ArrayCollection();
                $originalDesafios = new ArrayCollection();

                
                foreach ($practicante->getProyectos() as $proyecto) {
                    $originalProyectos->add($proyecto);
                }
                
                foreach ($practicante->getDesafios() as $desafio) {
                    $originalDesafios->add($desafio);
                }

                $practicante_form->submit($request);
                
                if($practicante_form->isValid())
                {
                    foreach ($originalProyectos as $proyecto) {
                        if (false === $practicante->getProyectos()->contains($proyecto)) {
                            $em->remove($proyecto);
                            foreach($proyecto->getTareas() as $tarea)
                                $em->remove ($tarea);
                        }
                    }
                    
                    $hoy = new \DateTime();
                    $diatom = date_format($hoy,'d')+1;
                    if($diatom>31)
                        $diatom=1;
                    $tomorrow = new \DateTime(date_format($hoy,'Y-m-').$diatom);
            
                    foreach($practicante->getProyectos() as $proyecto)
                    {
                        $proyecto->setPracticante($practicante);
                        $em->persist ($proyecto);
                        if(count($proyecto->getTareas())==0)
                        {
                            $tarea = new ProyectoTask();
                            $tarea->setFechaInicio($hoy);
                            $tarea->setFechaTermino($tomorrow);
                            $tarea->setNombre('Tarea 1');
                            $tarea->setProyecto($proyecto);
                            $proyecto->addTarea($tarea);
                            $em->persist($tarea);
                        }
                    }
                    
                    foreach ($originalDesafios as $desafio) {
                        if (false === $practicante->getDesafios()->contains($desafio)) {
                            $em->remove($desafio);
                        }
                    }
                    
                    foreach($practicante->getDesafios() as $desafio)
                    {
                        $desafio->setPracticante($practicante);
                        $em->persist ($desafio);
                    }
                    
                    $practicante->setSupervisor($supervisor);
                    $practicante->setAlumno($alumno);
                    $practicante->setOrganizacionAlias($organizacionAlias);
                    $practicante->setEstado(AlumnoPracticante::ESTADO_PENDIENTE);
                    
                    
                    $em->persist($practicante);
                    $em->flush();
                    
                    $idPracticante = $practicante->getId();
                    
                    return $this->redirect ($this->generateUrl ('practicas_alumno_wizard',array('step'=>5,'idAlumno'=>$idAlumno,'idOrganizacionAlias'=>$idOrganizacionAlias,'idSupervisor'=>$idSupervisor,'idPracticante'=>$idPracticante)));
                            
                }
            }
            
            
            $pjson = array();
            $count = 1;
            
            
            foreach($practicante->getProyectos() as $proyecto)
            {
                $series = array();
                

                foreach($proyecto->getTareas() as $tarea)
                {
                    $start_date = $tarea->getFechaInicio()?$tarea->getFechaInicio():new \DateTime();                    
                    $start = date_format($start_date,'Y,m-1,d');
                    $end_date = $tarea->getFechaTermino()?$tarea->getFechaTermino():new \DateTime();                    
                    $end = date_format($end_date,'Y,m-1,d');

                    $series[] = array(  'name'  =>  $tarea->getNombre(),
                                        'start' =>  'new Date('.$start.')',
                                        'end'   =>  'new Date('.$end.')',
                                        'idTarea'   => $tarea->getId()
                    );
                }
                
 
                $pjson[] = array('id'=>$count++,
                                'name'  => $proyecto->getNombre(),
                                'idProyecto'    => $proyecto->getId(),
                                'series'    => $series
                    );
            }         
            
            
            
            
            
            return $this->render(
                'pDevPracticasBundle:AlumnoPracticante:new5.html.twig',
                array(
                    'idOrganizacionAlias' => $organizacionAlias->getId(),
                    'idSupervisor' => $supervisor->getId(),
                    'idAlumno'  => $idAlumno,
                    'idPracticante' => $practicante->getId(),
                    'ganttdata' => json_encode($pjson))
            );
        }
        
        if($step==6)
        {
            if(!$practicante->getId())
                throw $this->createNotFoundException ('No se encontró practicante');
            
            $confirmForm = $this->createFormBuilder(array('id' => $practicante->getId()))
                ->add('id', 'hidden')
                ->getForm()
            ;
            
            if($request->isMethod('POST'))
            {               
                
                $confirmForm->submit($request);
                
                if($confirmForm->isValid()){
                    
                    
                    $practicante->setEstado(AlumnoPracticante::ESTADO_ENVIADA);
                    $em->persist($practicante);
                    $em->flush();
                    
                    return $this->redirect($this->generateUrl('practicas_alumno'));
                }
            }
            
            
            
            return $this->render(
                'pDevPracticasBundle:AlumnoPracticante:new6.html.twig',
                array(
                    'idOrganizacionAlias' => $organizacionAlias->getId(),
                    'idSupervisor' => $supervisor->getId(),
                    'idAlumno'  => $idAlumno,
                    'idPracticante' => $practicante->getId(),
                    'form'  => $confirmForm->createView()
                    )
            );
        }
        
        
        
    }

    /**
     * Finds and displays a AlumnoPracticante entity.
     *
     * @Route("/{id}/ganttdata", name="practicas_alumno_gantt_json")
     * @Method("GET")     
     */
    public function ganttDataAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $practicante = $em->getRepository('pDevPracticasBundle:AlumnoPracticante')->find($id);

        if (!$practicante) {
            throw $this->createNotFoundException('Unable to find AlumnoPracticante entity.');
        }

        $pjson = array();
        $count = 1;


        foreach($practicante->getProyectos() as $proyecto)
        {
            $series = array();


            foreach($proyecto->getTareas() as $tarea)
            {
                $start_date = $tarea->getFechaInicio()?$tarea->getFechaInicio():new \DateTime();                    
                $start = date_format($start_date,'Y,m-1,d');
                $end_date = $tarea->getFechaTermino()?$tarea->getFechaTermino():new \DateTime();                    
                $end = date_format($end_date,'Y,m-1,d');

                $series[] = array(  'name'  =>  $tarea->getNombre(),
                                    'start' =>  'new Date('.$start.')',
                                    'end'   =>  'new Date('.$end.')',
                                    'idTarea'   => $tarea->getId()
                );
            }


            $pjson[] = array('id'=>$count++,
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
     * Displays a form to edit an existing ProyectoTask entity.
     *
     * @Route("/proyecto/{idProyecto}/{idTarea}/edit", name="practicas_alumno_tarea")
     * @Template()
     */
    public function tareaAction($idProyecto,$idTarea)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('pDevPracticasBundle:Proyecto')->find($idProyecto);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Proyecto entity.');
        }
        
        if($idTarea==='new')
            $tarea = new ProyectoTask();
        else
            $tarea = $em->getRepository('pDevPracticasBundle:ProyectoTask')->find($idTarea);

        if (!$tarea) {
            throw $this->createNotFoundException('Unable to find ProyectoTask  entity.');
        }

        $editForm = $this->createForm(new ProyectoTaskType(), $tarea);        

        $request = $this->getRequest();
        if($request->isMethod('POST'))
        {
            $editForm->submit($request);
            if($editForm->isValid())
            {
                $tarea->setProyecto($entity);
                
                $em->persist($tarea);
                $em->flush();
                
                $response = new Response(json_encode(array('status'=>'ok')));
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
        }
            
        return array(
            'idProyecto'      => $idProyecto,
            'idTarea'      => $idTarea,
            'idPracticante'=> $entity->getPracticante()->getId(),
            'edit_form'   => $editForm->createView(),
            
        );
    }
    
    /**
     * Displays a form to edit an existing AlumnoPracticante entity.
     *
     * @Route("/proyecto/{idProyecto}/{idTarea}/remove", name="practicas_alumno_tarea_remove")
     * @Template()
     */
    public function tareaRemoveAction($idProyecto,$idTarea)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('pDevPracticasBundle:Proyecto')->find($idProyecto);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Proyecto entity.');
        }
        
        $tarea = $em->getRepository('pDevPracticasBundle:ProyectoTask')->find($idTarea);

        if (!$tarea) {
            throw $this->createNotFoundException('Unable to find ProyectoTask  entity.');
        }

        $editForm = $this->createDeleteForm($tarea->getId());        

        $request = $this->getRequest();
        if($request->isMethod('POST'))
        {
            $editForm->submit($request);
            if($editForm->isValid())
            {
                if(count($entity->getTareas())>1)
                    $em->remove($tarea);
                
                $em->flush();
                
                $response = new Response(json_encode(array('status'=>'ok')));
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
        }
            
        return array(
            'idProyecto'      => $idProyecto,
            'idTarea'      => $idTarea,
            'idPracticante'=> $entity->getPracticante()->getId(),
            'edit_form'   => $editForm->createView(),
            
        );
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
            'idPracticante'      => $practicante->getId(),
            'edit_form'   => $editForm->createView(),
         
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
    public function aceptarAction($id)
    {
        $pm = $this->get('permission.manager');
        $user = $pm->getUser();
        
        $isAlumno = $pm->checkType("TYPE_ALUMNO");
        $isSupervisor = $pm->checkType("TYPE_PRACTICAS_SUPERVISOR");
        $isContacto = $pm->checkType("TYPE_PRACTICAS_CONTACTO");
                
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('pDevPracticasBundle:AlumnoPracticante')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AlumnoPracticante entity.');
        }

        $aceptaForm = $this->createFormBuilder(array('id' => $id))
                        ->add('id', 'hidden')
                        ->getForm()
                    ;
        
        $request = $this->getRequest();
        if($request->isMethod('POST'))
        {
            $aceptaForm->submit($request);

            if ($aceptaForm->isValid()) {
                $estado = $entity->getEstado();
                if($estado === AlumnoPracticante::ESTADO_APROBADA)
                {
                    if($isAlumno)
                        $estado = AlumnoPracticante::ESTADO_ACEPTADA_ALUMNO;
                    elseif($isContacto or $isSupervisor)
                        $estado = AlumnoPracticante::ESTADO_ACEPTADA_SUPERVISOR;
                }
                elseif($estado === AlumnoPracticante::ESTADO_ACEPTADA_ALUMNO or $estado === AlumnoPracticante::ESTADO_ACEPTADA_SUPERVISOR)
                    $estado = AlumnoPracticante::ESTADO_ACEPTADA;
                
                $entity->setEstado($estado);
                $em->persist($entity);
                $em->flush();

                return $this->redirect($this->generateUrl('practicas_alumno_show', array('id' => $id)));
            }
        }
        
        return array(
            'entity'      => $entity,
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
        $isAlumno = $pm->checkType("TYPE_ALUMNO");
        $isSupervisor = $pm->checkType("TYPE_PRACTICAS_SUPERVISOR");
        $isContacto = $pm->checkType("TYPE_PRACTICAS_CONTACTO");
        $isCoordinacion = $pm->isGranted("ROLE_ADMIN","SITE_PRACTICAS");
        $isAcademico = $pm->checkType("TYPE_ACADEMICO");
        
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
                    $start = date_format($start_date,'Y,m-1,d');
                    $end_date = $tarea->getFechaTermino()?$tarea->getFechaTermino():new \DateTime();                    
                    $end = date_format($end_date,'Y,m-1,d');

                    $series[] = array(  'name'  =>  $tarea->getNombre(),
                                        'start' =>  'new Date('.$start.')',
                                        'end'   =>  'new Date('.$end.')',
                                        'idTarea'   => $tarea->getId()
                    );
                }
                
 
                $pjson[] = array('id'=>$count++,
                                'name'  => $proyecto->getNombre(),
                                'idProyecto'    => $proyecto->getId(),
                                'series'    => $series
                    );
            }         
            
            
            
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
            'ganttdata' => json_encode($pjson),
            'isCoordinacion'=>$isCoordinacion,
            'isAcademico'=>$isAcademico,
            'isAlumno'  => $isAlumno,
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

