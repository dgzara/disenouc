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
use pDev\PracticasBundle\Entity\Practica;
use pDev\PracticasBundle\Entity\Contacto;

use pDev\PracticasBundle\Form\AlumnoPracticanteType;
use pDev\PracticasBundle\Form\AlumnoPracticanteProfesorType;
use pDev\PracticasBundle\Form\AlumnoType;
use pDev\PracticasBundle\Form\SupervisorType;
use pDev\PracticasBundle\Form\SupervisorOrganizacionType;
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
     * @Route("/", name="practicas_alumno")
     * @Template()
     */
    public function indexAction(Request $request)
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
        $repo = $em->getRepository('pDevPracticasBundle:AlumnoPracticante');
        $consulta = $repo->createQueryBuilder('p')
                    ->leftJoin('p.alumno','a')
                    ->leftJoin('p.supervisor','s')
                    ->leftJoin('p.organizacionAlias','oa')
                    //->leftJoin('oa.organizacion','o')
                    ->leftJoin('oa.practicas','pr')
                    ->leftJoin('pr.contacto','c')
                    ->leftJoin('p.profesor','prof');
        
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
        
        $query = $consulta->orderBy('p.id', 'DESC');
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1)/*page number*/,
            10/*limit per page*/
        );
                
        return array(
            'pagination' => $pagination,
            'isExterno'=> $isExterno,
            'idAlumno'  => $isAlumno?$alumno->getId():false,
            'isAlumno' => $isAlumno,
            'isSupervisor'=> $isSupervisor,            
        );
    }
    
    /**
     * Lists all AlumnoPracticante entities.
     *
     * @Route("/excel/{estado}/{periodo}", name="practicas_alumno_excel")
     * @Template()
     */
    public function excelAction(Reuqest $request)
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
            ->add('organizacionAlias', 'hidden', array(
                'mapped' => false,
                'required' => true
            ))
            ->getForm();
        
        // Generamos el formulario nuevo
        $organizacionAlias = new OrganizacionAlias();
        $organizacion = new Organizacion();
        $supervisor = new Supervisor();
        
        $organizacionAlias_form = $this->createForm(new OrganizacionAliasType(), $organizacionAlias);
        $organizacion_form = $this->createForm(new OrganizacionType(), $organizacion);
        $supervisor_form = $this->createForm(new SupervisorOrganizacionType(), $supervisor);
        
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
            ->add('organizacionAlias', 'hidden', array(
                'mapped' => false,
                'required' => true
            ))
            ->getForm();
            
        // Generamos el formulario nuevo
        $organizacionAlias = new OrganizacionAlias();
        $organizacion = new Organizacion();
        $supervisor = new Supervisor();
        
        $organizacionAlias_form = $this->createForm(new OrganizacionAliasType(), $organizacionAlias);
        $organizacion_form = $this->createForm(new OrganizacionType(), $organizacion);
        $supervisor_form = $this->createForm(new SupervisorOrganizacionType(), $supervisor);
        
        if($request->isMethod('POST'))
        {
            $form->submit($request);
            $organizacionAlias_form->submit($request);
            $organizacion_form->submit($request);
            $supervisor_form->submit($request);
            
            if($form->isValid())
            {
                $id = $form->get('organizacionAlias')->getData();
                $organizacionAlias = $em->getRepository('pDevPracticasBundle:OrganizacionAlias')->find($id);
                
                if(!$organizacionAlias)
                    throw $this->createNotFoundException('Unable to find OrganizacionAlias entity.');                   
                    
                return $this->redirect ($this->generateUrl ('practicas_alumno_new_datos',array('idOrganizacionAlias' => $organizacionAlias->getId())));
            }
            elseif($organizacion_form->isValid() and $organizacionAlias_form->isValid() and $supervisor_form->isValid())
            {
                // Creamos el usuario
                $userManager = $this->container->get('fos_user.user_manager');
                $user = $userManager->createUser();
                $user->setRut($supervisor->getRut());
                $user->setEmail($supervisor->getEmail());
                $user->setNombres($supervisor->getNombres());
                $user->setApellidoPaterno($supervisor->getApellidoPaterno());
                $user->setApellidoMaterno($supervisor->getApellidoMaterno());
                $user->setUsername($user->getEmail());
                $user->setExternal(true);
                $user->setEnabled(true);
                
                // Guardamos los datos del supervisor y organizacion
                $organizacionAlias->setOrganizacion($organizacion);
                $supervisor->addOrganizacion($organizacion);
                $supervisor->setUsuario($user);
                
                // Creamos el contacto
                $contacto = new Contacto();
                $contacto->setRut($supervisor->getRut());
                $contacto->setNombres($supervisor->getNombres());
                $contacto->setApellidoPaterno($supervisor->getApellidoPaterno());
                $contacto->setApellidoMaterno($supervisor->getApellidoMaterno());
                $contacto->setEmail($supervisor->getEmail());
                $contacto->setUsuario($user);
                
                // Seteamos la contraseña
                $password = $user->getPassword();
                $user->setSalt(md5(time()));
                $factory = $this->get('security.encoder_factory');
                $encoder = $factory->getEncoder($user);
                $password = $encoder->encodePassword($user->getPassword(), $user->getSalt());
                $user->setPassword($password);
                $user->addRole("ROLE_USER");
                
                // Creamos el usuario y mandamos el mail
                $tokenGenerator = $this->get('fos_user.util.token_generator');
                $user->setConfirmationToken($tokenGenerator->generateToken());
                $this->get('fos_user.mailer')->sendResettingEmailMessage($user);
                $user->setPasswordRequestedAt(new \DateTime());
                $this->get('fos_user.user_manager')->updateUser($user);
                
                // Guardamos
                $em->persist($user);
                $em->persist($contacto);
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
     * @Route("/new/datos/practica/{practicaId}", name="practicas_alumno_new_datos_practica")
     * @Route("/new/datos/{id}", name="practicas_alumno_new_datos_source")
     * @Template()
     */
    public function datosAction(Request $request, $id = null, $idOrganizacionAlias = null, $practicaId = null)
    {
        $pm = $this->get('permission.manager');
        $em = $this->getDoctrine()->getManager();
        $user = $pm->getUser();
        $alumno = $user->getPersona('TYPE_ALUMNO');
        
        if(!$alumno) {
            throw $this->createNotFoundException('Unable to find Alumno entity.');
        }
        
        // Si hay una practica asociada, la adjuntamos
        if($practicaId)
        {
            $practica = $em->getRepository('pDevPracticasBundle:Practica')->find($practicaId);

            if (!$practica) {
                throw $this->createNotFoundException('Unable to find Practica entity.');
            }
            
            // Creamos la práctica
            $entity = new AlumnoPracticante();
            $entity->setAlumno($alumno);
            $entity->setPractica($practica);
            $entity->setOrganizacionAlias($practica->getOrganizacionAlias());
            $entity->setSupervisor($practica->getSupervisor());
            $entity->setComoContacto("Ofertas publicadas en este sitio");
            $entity->setFechaInicio($practica->getFechaInicio());
            $entity->setDuracionCantidad($practica->getDuracionCantidad());
            $entity->setDuracionUnidad($practica->getDuracionUnidad());
            $entity->setTipo($practica->getTipo());
            $entity->setEstado(AlumnoPracticante::ESTADO_POSTULADO);
            
            $em->persist($entity);
            $em->flush();
            
            $request->getSession()->getFlashBag()->add(
                'notice',
                'Practica postulada'
            );
            
            return $this->redirect($this->generateUrl('practicas_show', array('id' => $practica->getId())));
        }
        elseif($id)
        {
            $entity = $em->getRepository('pDevPracticasBundle:AlumnoPracticante')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find AlumnoPracticante entity.');
            }
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
            
            $entity = new AlumnoPracticante();
            $entity->setAlumno($alumno);
            $entity->setOrganizacionAlias($organizacionAlias);
            
            if($supervisor)
                $entity->setSupervisor($supervisor);
        }
        
        // Generamos los datos defecto
        if($entity->getDesafios()->count() == 0){
            $entity->addDesafio(new Desafio());
            $entity->addDesafio(new Desafio());
            $entity->addDesafio(new Desafio());
            $entity->addDesafio(new Desafio());
            $entity->addDesafio(new Desafio());
        }
        if($entity->getProyectos()->count() == 0){
            $entity->addProyecto(new Proyecto());
            $entity->addProyecto(new Proyecto());
            $entity->addProyecto(new Proyecto());
        }
        
        $form = $this->createForm(new AlumnoPracticanteType(), $entity);
        
        // Eliminamos los datos
        $form->remove('organizacionAlias');
        $form->remove('contacto');
        $form->remove('supervisor');
            
        // Si esta asociada a una practica, borramos los campos de organizacion y contacto
        if($entity->getPractica()){
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
     * @Route("/datos/create/{id}", name="practicas_alumno_create_datos_source")
     * @Method("POST")
     * @Template("pDevPracticasBundle:AlumnoPracticante:datos.html.twig")
     */
    public function datosCreateAction(Request $request, $id = null, $idOrganizacionAlias = null)
    {
        $pm = $this->get('permission.manager');
        $user = $pm->getUser();
        $em = $this->getDoctrine()->getManager();
        $alumno = $user->getPersona('TYPE_ALUMNO');
        
        if(!$alumno) {
            throw $this->createNotFoundException('Unable to find Alumno entity.');
        }
        
        // Buscamos el AlumnoPracticante asociado
        if($id)
        {
            $entity = $em->getRepository('pDevPracticasBundle:AlumnoPracticante')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find AlumnoPracticante entity.');
            }
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
            
            $entity = new AlumnoPracticante();
            $entity->setAlumno($alumno);
            $entity->setOrganizacionAlias($organizacionAlias);
            
            if($supervisor)
                $entity->setSupervisor($supervisor);
        }
        
        // Generamos los datos defecto
        if($entity->getDesafios()->count() == 0){
            $entity->addDesafio(new Desafio());
            $entity->addDesafio(new Desafio());
            $entity->addDesafio(new Desafio());
            $entity->addDesafio(new Desafio());
            $entity->addDesafio(new Desafio());
        }
        if($entity->getProyectos()->count() == 0){
            $entity->addProyecto(new Proyecto());
            $entity->addProyecto(new Proyecto());
            $entity->addProyecto(new Proyecto());
        }
        
        $form = $this->createForm(new AlumnoPracticanteType(), $entity);

        // Eliminamos los datos
        $form->remove('organizacionAlias');
        $form->remove('contacto');
        $form->remove('supervisor');
            
        // Si esta asociada a una practica, borramos los campos de organizacion y contacto
        if($entity->getPractica()){
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
            // Generamos las fechas
            $tomorrow = new \DateTime();
            $tomorrow->modify('+1 day');
            
            // Creamos las tareas si no las posee
            foreach($entity->getProyectos() as $proyecto)
            {
                if($proyecto->getTareas()->count() == 0)
                {
                    $tarea = new ProyectoTask();
                    $tarea->setFechaInicio(new \DateTime());
                    $tarea->setFechaTermino($tomorrow);
                    $tarea->setNombre('Tarea 1');
                    $tarea->setProyecto($proyecto);
                    $proyecto->addTarea($tarea);
                    $em->persist($tarea);
                }
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
            
            $request->getSession()->getFlashBag()->add(
                'notice',
                'El plan de práctica ha sido enviada a revisión'
            );
            
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
                $start_date = $tarea->getFechaInicio() ? $tarea->getFechaInicio():new \DateTime();                    
                $start = date_format($start_date,'Y,m-1,d');
                $end_date = $tarea->getFechaTermino() ? $tarea->getFechaTermino():new \DateTime();                    
                $end = date_format($end_date,'Y,m-1,d');

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

            if ($editForm->isValid()) 
            {
                $em->persist($entity);
                $em->flush();

                $request->getSession()->getFlashBag()->add(
                    'notice',
                    'El plan de práctica ha cambiado de estado: '.$entity->getEstado()
                );
                
                // Devuelve la ruta
                $array = array('redirect' => $this->generateUrl('practicas_alumno_show', array('id' => $id))); // data to return via JSON
                $response = new Response(json_encode($array));
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
        
        $isAlumno = $pm->checkType("TYPE_ALUMNO") and $entity->hasAlumno($user->getPersona('TYPE_ALUMNO'));
        $isSupervisor = $pm->checkType("TYPE_PRACTICAS_SUPERVISOR") and $entity->hasSupervisor($user->getPersona('TYPE_PRACTICAS_SUPERVISOR'));
        $isContacto = $pm->checkType("TYPE_PRACTICAS_CONTACTO") and $entity->hasContacto($user->getPersona('TYPE_PRACTICAS_CONTACTO'));
        $isAcademico = $pm->checkType("TYPE_ACADEMICO") and $entity->hasAcademico($user->getPersona('TYPE_ACADEMICO'));
        $isCoordinacion = $pm->isGranted("ROLE_ADMIN","SITE_PRACTICAS");

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
                $mensaje = "";

                if(($isContacto or $isSupervisor) and $estado === AlumnoPracticante::ESTADO_POSTULADO)
                {
                    $estado = AlumnoPracticante::ESTADO_ACEPTADA_CONTACTO;
                    $mensaje = "Ha aceptado al postulante";
                }
                elseif(($isContacto or $isSupervisor) and $estado === AlumnoPracticante::ESTADO_ENVIADA)
                {
                    $estado = AlumnoPracticante::ESTADO_ACEPTADA_SUPERVISOR;
                    $mensaje = "Ha aceptado el plan de práctica";
                }
                elseif($isCoordinacion and $estado === AlumnoPracticante::ESTADO_ACEPTADA_SUPERVISOR)
                {
                    $mensaje = "Ha aceptado el plan de práctica";
                    $estado = AlumnoPracticante::ESTADO_APROBADA;
                }
                elseif($isAlumno and $estado === AlumnoPracticante::ESTADO_APROBADA)
                {
                    $estado = AlumnoPracticante::ESTADO_INICIADA;
                    $mensaje = "Ha iniciado la práctica";
                }
                elseif($isAlumno and $estado === AlumnoPracticante::ESTADO_INICIADA)
                {
                    $estado = AlumnoPracticante::ESTADO_TERMINADA;
                    $mensaje = "Ha finalizado la práctica";
                }
                elseif($isCoordinacion and $estado === AlumnoPracticante::ESTADO_INFORME)
                {
                    $estado = AlumnoPracticante::ESTADO_EVALUADA;
                    $mensaje = "Ha sido evaluada la práctica";
                }
                
                $entity->setEstado($estado);
                $em->persist($entity);
                $em->flush();
                
                // Envio de mensaje
                if($mensaje != "")
                {
                    $request->getSession()->getFlashBag()->add(
                        'notice',
                        $mensaje
                    );
                }
                
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
     * Displays a form to edit an existing Practica entity.
     *
     * @Route("/postulante/{id}/aceptar", name="practicas_alumno_postulante_aceptar")
     * @Template()
     */
    public function aceptarPostulanteAction(Request $request, $id)
    {
        $pm = $this->get('permission.manager');
        $user = $pm->getUser();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('pDevPracticasBundle:AlumnoPracticante')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AlumnoPracticante entity.');
        }
        
        $isAlumno = $pm->checkType("TYPE_ALUMNO") and $entity->hasAlumno($user->getPersona('TYPE_ALUMNO'));
        $isSupervisor = $pm->checkType("TYPE_PRACTICAS_SUPERVISOR") and $entity->hasSupervisor($user->getPersona('TYPE_PRACTICAS_SUPERVISOR'));
        $isContacto = $pm->checkType("TYPE_PRACTICAS_CONTACTO") and $entity->hasContacto($user->getPersona('TYPE_PRACTICAS_CONTACTO'));
        $isAcademico = $pm->checkType("TYPE_ACADEMICO") and $entity->hasAcademico($user->getPersona('TYPE_ACADEMICO'));
        $isCoordinacion = $pm->isGranted("ROLE_ADMIN","SITE_PRACTICAS");
        
        $confirmForm = $this->createConfirmForm($id);
        
        if($request->isMethod('POST'))
        {
            $confirmForm->submit($request);
            if ($confirmForm->isValid()) 
            {
                $estado = $entity->getEstado();
                
                if($isContacto and $estado === AlumnoPracticante::ESTADO_POSTULADO)
                {
                    $estado = AlumnoPracticante::ESTADO_ACEPTADA_CONTACTO;
                }
                
                // Descontamos los cupos
                $practica = $entity->getPractica();
                $cuposRestantes = $practica->getCupos() - 1;
                $practica->setCupos($cuposRestantes);
                
                if($cuposRestantes == 0)
                {
                    $estadoPractica = Practica::ESTADO_FINALIZADA;
                    $practica->setEstado($estadoPractica);
                }
                
                $entity->setEstado($estado);
                $em->persist($entity);
                $em->persist($practica);
                $em->flush();
                
                $request->getSession()->getFlashBag()->add(
                    'notice',
                    'Ha aceptado al postulante '.$entity->getAlumno()
                );
                
                // Devuelve la ruta
                $array = array('redirect' => $this->generateUrl('practicas_show', array('id' => $entity->getPractica()->getId()))); // data to return via JSON
                $response = new Response(json_encode($array));
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
        }
        
        return array(
            'entity' => $entity,
            'form'   => $confirmForm->createView(),
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
        
        // Verificamos que es el alumno relacionado
        if($pm->checkType("TYPE_ALUMNO") and $entity->hasAlumno($user->getPersona('TYPE_ALUMNO')))
            $isAlumno = true;
        
        // Verificamos que es el supevisor relacionado
        if($pm->checkType("TYPE_PRACTICAS_SUPERVISOR") and $entity->hasSupervisor($user->getPersona('TYPE_PRACTICAS_SUPERVISOR')))
            $isSupervisor = true;
            
        // Verificamos que es el contacto relacionado
        if($pm->checkType("TYPE_PRACTICAS_CONTACTO") and $entity->hasContacto($user->getPersona('TYPE_PRACTICAS_CONTACTO')))
            $isContacto = true;
            
        // Verificamos que es el profesor relacionado
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

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
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

        $editForm = $this->createForm(new AlumnoPracticanteType(), $entity);
        $editForm->submit($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            $request->getSession()->getFlashBag()->add(
                'notice',
                'El plan de práctica ha sido actualizado'
            );
                    
            return $this->redirect($this->generateUrl('practicas_alumno_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
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
            
            $request->getSession()->getFlashBag()->add(
                'notice',
                'El plan de práctica ha sido borrado'
            );
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
     * Asignar un profesor
     *
     * @Route("/{id}/asignar", name="practicas_alumno_asignar_profesor")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function asignarProfesorAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('pDevPracticasBundle:AlumnoPracticante')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AlumnoPracticante entity.');
        }

        $editForm = $this->createForm(new AlumnoPracticanteProfesorType(), $entity);
        
        if($request->isMethod('POST'))
        {
            $editForm->submit($request);
            if ($editForm->isValid()) 
            {
                $em->persist($entity);
                $em->flush();

                $request->getSession()->getFlashBag()->add(
                    'notice',
                    'El profesor '.$entity->getProfesor().' fue asignado para esta práctica'
                );
            }
            // Devuelve la ruta
            $array = array('redirect' => $this->generateUrl('practicas_alumno_show', array('id' => $id))); // data to return via JSON
            $response = new Response(json_encode($array));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        
        return array(
            'entity' => $entity,
            'form'   => $editForm->createView(),
        );   
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
}

