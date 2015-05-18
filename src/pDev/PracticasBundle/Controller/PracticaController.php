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
use pDev\PracticasBundle\Entity\Contacto;
use pDev\PracticasBundle\Form\PracticaType;
use pDev\PracticasBundle\Form\OrganizacionType;
use pDev\PracticasBundle\Form\ContactoType;
use pDev\PracticasBundle\Form\PracticaEstadoType;

/**
 * Practica controller.
 *
 * @Route("/practicas/ofertas")
 */
class PracticaController extends Controller
{
    /**
     * Lists all Practica entities.
     *
     * @Route("/", name="practicas")
     * @Template()
     */
    public function indexAction()
    {
        $pm = $this->get('permission.manager');
        $user = $pm->getUser();
        
        $isExterno = $user->getExternal();
        $isAlumno = $pm->checkType("TYPE_ALUMNO");
        $isSupervisor = $pm->checkType("TYPE_PRACTICAS_SUPERVISOR");
        $isContacto = $pm->checkType("TYPE_PRACTICAS_CONTACTO");
        $isCoordinacion = $pm->isGranted("ROLE_ADMIN","SITE_PRACTICAS");
        
        $em = $this->getDoctrine()->getManager();
        
        $qb = $em->getRepository('pDevPracticasBundle:Practica')->createQueryBuilder('p');
        $entities = $qb->leftJoin('p.creador','cr')
                    ->leftJoin('p.contacto','co')
                    ->leftJoin('p.organizacion','o');
        
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
        
        $query = $entities->orderBy('p.id', 'DESC');

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
        );
    }
    
    public function excelACtion(Request $request)
    {
        $entities = $entities->orderBy($orderBy, $order)                    
                ->getQuery()
                ->getResult();
        $excelService = $this->get('xls.service_xls2007');

        $excelService->excelObj->getProperties()->setCreator($this->getUser()->getNombrecompleto())
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
            $excelService->excelObj->getActiveSheet()->setCellValueByColumnAndRow($ec, $ef, $entity->getOrganizacion()->getNombre());
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
     * @Route("/create/organizacion/{id}", name="practicas_create_organizacion")
     * @Method("POST")
     * @Template("pDevPracticasBundle:Practica:new.html.twig")
     */
    public function createAction(Request $request, $id = null)
    {   
        $pm = $this->get('permission.manager');
        $em = $this->getDoctrine()->getManager();
        $user = $pm->getUser();
        
        $entity = new Practica();
        
        // Si es un contacto, lo agrega
        $isContacto = $pm->checkType("TYPE_PRACTICAS_CONTACTO");  
        if($isContacto){
            $contacto = $user->getPersona('TYPE_PRACTICAS_CONTACTO');
            $entity->setContacto($contacto);
        }
        
        $securityContext = $this->container->get('security.context');
        $ruta = $this->generateUrl('practicas_create');
        
        // Creamos el formulario
        $form = $this->createForm(new PracticaType($securityContext, null), $entity);
                    
        // Si es un contacto, lo agrega
        $isContacto = $pm->checkType("TYPE_PRACTICAS_CONTACTO");         
        if($isContacto)
        {   
            // Creamos el campo
            $form->remove('contacto');
            $form->remove('tipo');
            
            $contacto = $user->getPersona('TYPE_PRACTICAS_CONTACTO');
            $entity->setContacto($contacto);
        }
        
        // Comprobamos si fue realizada desde una organizacion
        if($id)
        {
            $organizacion = $em->getRepository('pDevPracticasBundle:Organizacion')->find($id);
            if (!$organizacion) {
                throw $this->createNotFoundException('Unable to find Organizacion entity.');
            }
            $entity->setOrganizacion($organizacion);
            
            // Creamos el formulario
            $form = $this->createForm(new PracticaType($securityContext, $organizacion), $entity);
            $form->remove('organizacion');

            $ruta = $this->generateUrl('practicas_create_organizacion', array('id' => $id));
        }
        
        $form->handleRequest($request);

        if ($form->isValid()) 
        {   
            $em = $this->getDoctrine()->getManager();
            $entity->setCreador($this->getUser());
            
            // Comprobamos que el supervisor existe
            $supervisor = $entity->getSupervisor();
            
            // Buscamos primero por email
            $supervisorBuscado = $em->getRepository('pDevPracticasBundle:Supervisor')->findOneByEmail($supervisor->getEmail());
            
            // Si existe, lo añadimos
            if($supervisorBuscado)
            {
                $entity->setSupervisor($supervisorBuscado);
                $supervisorBuscado->addPractica($entity);
                $em->persist($supervisorBuscado);
            }
            else
            {
                // Creamos al supervisor
                $em->persist($supervisor);
                
                // Revisamos si el correo / rut está asociado a un usuario.
                $usuario = $em->getRepository('pDevUserBundle:User')->findOneByEmail($supervisor->getEmail());
                
                // Probamos con el rut
                if(!$usuario)
                    $usuario = $em->getRepository('pDevUserBundle:User')->findOneByRut($supervisor->getRut()); 
                
                // Si lo encuentra, lo añade
                if($usuario){
                    $supervisor->setUsuario($usuario);
                } else {
                    // Creamos el usuario
                    $userManager = $this->container->get('fos_user.user_manager');
                    $usuario = $userManager->createUser();
                    $usuario->setRut($supervisor->getRut());
                    $usuario->setEmail($supervisor->getEmail());
                    $usuario->setNombres($supervisor->getNombres());
                    $usuario->setApellidoPaterno($supervisor->getApellidoPaterno());
                    $usuario->setApellidoMaterno($supervisor->getApellidoMaterno());
                    $usuario->setUsername($supervisor->getEmail());
                    $usuario->setExternal(true);
                    $usuario->setEnabled(true);
                    
                    // Establecemos el usuario a este supervisor
                    $supervisor->setUsuario($usuario);
                    
                    // Seteamos la contraseña
                    $password = $usuario->getPassword();
                    $usuario->setSalt(md5(time()));
                    $factory = $this->get('security.encoder_factory');
                    $encoder = $factory->getEncoder($usuario);
                    $password = $encoder->encodePassword($usuario->getPassword(), $usuario->getSalt());
                    $usuario->setPassword($password);
                    $usuario->addRole("ROLE_USER");
                    
                    // Creamos el usuario y mandamos el mail
                    $tokenGenerator = $this->get('fos_user.util.token_generator');
                    $usuario->setConfirmationToken($tokenGenerator->generateToken());
                    $this->get('fos_user.mailer')->sendResettingEmailMessage($usuario);
                    $usuario->setPasswordRequestedAt(new \DateTime());
                    $this->get('fos_user.user_manager')->updateUser($usuario);
                    $em->persist($usuario);
                }
            }
            
            // Comprobamos que el contacto existe
            $contacto = $entity->getContacto();
            
            // Buscamos primero por email
            $contactoBuscado = $em->getRepository('pDevPracticasBundle:Contacto')->findOneByEmail($contacto->getEmail());
            
            // Si existe, lo añadimos
            if($contactoBuscado)
            {
                $entity->setContacto($contactoBuscado);
                $contactoBuscado->addPractica($entity);
                $em->persist($contactoBuscado);
            }
            else
            {
                // Creamos al contacto
                $em->persist($contacto);
                
                // Revisamos si el correo / rut está asociado a un usuario.
                $usuarioContacto = $em->getRepository('pDevUserBundle:User')->findOneByEmail($contacto->getEmail());
                
                // Probamos con el rut
                if(!$usuarioContacto)
                    $usuarioContacto = $em->getRepository('pDevUserBundle:User')->findOneByRut($contacto->getRut()); 
                
                // Si lo encuentra, lo añade
                if($usuarioContacto){
                    $contacto->setUsuario($usuarioContacto);
                } else {
                    // Creamos el usuario
                    $userManager = $this->container->get('fos_user.user_manager');
                    $usuarioContacto = $userManager->createUser();
                    $usuarioContacto->setRut($contacto->getRut());
                    $usuarioContacto->setEmail($contacto->getEmail());
                    $usuarioContacto->setNombres($contacto->getNombres());
                    $usuarioContacto->setApellidoPaterno($contacto->getApellidoPaterno());
                    $usuarioContacto->setApellidoMaterno($contacto->getApellidoMaterno());
                    $usuarioContacto->setUsername($contacto->getEmail());
                    $usuarioContacto->setExternal(true);
                    $usuarioContacto->setEnabled(true);
                    
                    // Establecemos el usuario a este supervisor
                    $contacto->setUsuario($usuarioContacto);
                    
                    // Seteamos la contraseña
                    $password = $usuarioContacto->getPassword();
                    $usuarioContacto->setSalt(md5(time()));
                    $factory = $this->get('security.encoder_factory');
                    $encoder = $factory->getEncoder($usuarioContacto);
                    $password = $encoder->encodePassword($usuarioContacto->getPassword(), $usuarioContacto->getSalt());
                    $usuarioContacto->setPassword($password);
                    $usuarioContacto->addRole("ROLE_USER");
                    
                    // Creamos el usuario y mandamos el mail
                    $tokenGenerator = $this->get('fos_user.util.token_generator');
                    $usuarioContacto->setConfirmationToken($tokenGenerator->generateToken());
                    $this->get('fos_user.mailer')->sendResettingEmailMessage($usuarioContacto);
                    $usuarioContacto->setPasswordRequestedAt(new \DateTime());
                    $this->get('fos_user.user_manager')->updateUser($usuarioContacto);
                    $em->persist($usuarioContacto);
                }
            }
            
            $em->persist($entity);
            $em->flush();
            
            $request->getSession()->getFlashBag()->add(
                'notice',
                'La práctica ha sido enviada a revisión'
            );
        
            return $this->redirect($this->generateUrl('practicas_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'ruta' => $ruta
        );
    }

    /**
     * Displays a form to create a new Practica entity.
     *
     * @Route("/new", name="practicas_new")
     * @Route("/organizacion/{id}", name="practicas_new_organizacion")
     * @Method("GET")
     * @Template()
     */
    public function newAction($id = null)
    {
        $pm = $this->get('permission.manager');
        $em = $this->getDoctrine()->getManager();
        $user = $pm->getUser();
        
        $entity = new Practica();
        
        $securityContext = $this->container->get('security.context');
        $ruta = $this->generateUrl('practicas_create');
        
        // Creamos el formulario
        $form = $this->createForm(new PracticaType($securityContext, null), $entity);
        
        // Si es un contacto, lo agrega
        $isContacto = $pm->checkType("TYPE_PRACTICAS_CONTACTO");         
        if($isContacto)
        {   
            // Creamos el campo
            $form->remove('contacto');
            $form->remove('tipo');
            
            $contacto = $user->getPersona('TYPE_PRACTICAS_CONTACTO');
            $entity->setContacto($contacto);
        }
        
        // Comprobamos si fue realizada desde una organizacion
        if($id)
        {
            $organizacion = $em->getRepository('pDevPracticasBundle:Organizacion')->find($id);
            if (!$organizacion) {
                throw $this->createNotFoundException('Unable to find Organizacion entity.');
            }
            $entity->setOrganizacion($organizacion);
            
            // Creamos el formulario
            $form = $this->createForm(new PracticaType($securityContext, $organizacion), $entity);
            $form->remove('organizacion');

            $ruta = $this->generateUrl('practicas_create_organizacion', array('id' => $id));
        }
        
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'ruta' => $ruta
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
            $alumnoPractica = $em->getRepository('pDevPracticasBundle:AlumnoPracticante')->findOneBy(array("alumno" => $user->getPersona('TYPE_ALUMNO'), "practica" => $entity));
        
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
        // Permisos
        $pm = $this->get('permission.manager');
        $isCoordinacion = $pm->isGranted("ROLE_ADMIN","SITE_PRACTICAS");
                
        // Cargamos la entidad
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('pDevPracticasBundle:Practica')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Practica entity.');
        }

        // Revisamos que sea el coordinador o el contacto
        if(!$isCoordinacion && !$entity->hasContacto($this->getUser()->getPersona('TYPE_PRACTICAS_CONTACTO'))){
            return $this->redirect($this->generateUrl('practicas_show', array('id' => $id)));
        }
        
        // Generamos el formulario
        $securityContext = $this->container->get('security.context');
        $editForm = $this->createForm(new PracticaType($securityContext, $entity->getOrganizacion()), $entity);
        $editForm->remove('organizacion');
        
        if($pm->isGranted("ROLE_ADMIN","SITE_PRACTICAS") === false){
            $editForm->remove('tipo');
            $editForm->remove('contacto');
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
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
        // Permisos
        $pm = $this->get('permission.manager');
        $isCoordinacion = $pm->isGranted("ROLE_ADMIN","SITE_PRACTICAS");
                
        // Cargamos la entidad
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('pDevPracticasBundle:Practica')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Practica entity.');
        }
        
        // Revisamos que sea el coordinador o el contacto
        if(!$isCoordinacion and !$entity->hasContacto($this->getUser()->getPersona('TYPE_PRACTICAS_CONTACTO'))){
            return $this->redirect($this->generateUrl('practicas_show', array('id' => $id)));
        }
        
        // Obtenemos el supervisor original
        $originalSupervisor = clone $entity->getSupervisor();
        
        // Obtenemos el contacto original
        $originalContacto = clone $entity->getContacto();

        // Creamos el formulario
        $securityContext = $this->container->get('security.context');
        $editForm = $this->createForm(new PracticaType($securityContext, $entity->getOrganizacion()), $entity);
        $editForm->remove('organizacion');
        
        if($pm->isGranted("ROLE_ADMIN","SITE_PRACTICAS") === false){
            $editForm->remove('tipo');
            $editForm->remove('contacto');
        }
            
        $editForm->submit($request);

        if ($editForm->isValid()) 
        {
            // Si la modifica el usuario, nuevamente es enviada al coordinador
            if($this->getUser() === $entity->getCreador())
                $entity->setEstado(Practica::ESTADO_REVISION);
            
            // Comprobamos que el supervisor existe
            $supervisor = $entity->getSupervisor();
            
            // Revisamos si hubo cambio
            if($supervisor !== $originalSupervisor)
            {
                // Sacamos al supervisor Original
                $originalSupervisor->removePractica($entity);
                $em->persist($originalSupervisor);
                
                // Buscamos primero por email
                $supervisorBuscado = $em->getRepository('pDevPracticasBundle:Supervisor')->findOneByEmail($supervisor->getEmail());
                
                // Si existe, lo añadimos
                if($supervisorBuscado)
                {
                    $entity->setSupervisor($supervisorBuscado);
                    $supervisorBuscado->addPractica($entity);
                    $em->persist($supervisorBuscado);
                }
                else
                {
                    // Creamos al supervisor
                    $em->persist($supervisor);
                    
                    // Revisamos si el correo / rut está asociado a un usuario.
                    $usuario = $em->getRepository('pDevUserBundle:User')->findOneByEmail($supervisor->getEmail());
                    
                    // Probamos con el rut
                    if(!$usuario)
                        $usuario = $em->getRepository('pDevUserBundle:User')->findOneByRut($supervisor->getRut()); 
                    
                    // Si lo encuentra, lo añade
                    if($usuario){
                        $supervisor->setUsuario($usuario);
                    } else {
                        // Creamos el usuario
                        $userManager = $this->container->get('fos_user.user_manager');
                        $usuario = $userManager->createUser();
                        $usuario->setRut($supervisor->getRut());
                        $usuario->setEmail($supervisor->getEmail());
                        $usuario->setNombres($supervisor->getNombres());
                        $usuario->setApellidoPaterno($supervisor->getApellidoPaterno());
                        $usuario->setApellidoMaterno($supervisor->getApellidoMaterno());
                        $usuario->setUsername($supervisor->getEmail());
                        $usuario->setExternal(true);
                        $usuario->setEnabled(true);
                        
                        // Establecemos el usuario a este supervisor
                        $supervisor->setUsuario($usuario);
                        
                        // Seteamos la contraseña
                        $password = $usuario->getPassword();
                        $usuario->setSalt(md5(time()));
                        $factory = $this->get('security.encoder_factory');
                        $encoder = $factory->getEncoder($usuario);
                        $password = $encoder->encodePassword($usuario->getPassword(), $usuario->getSalt());
                        $usuario->setPassword($password);
                        $usuario->addRole("ROLE_USER");
                        
                        // Creamos el usuario y mandamos el mail
                        $tokenGenerator = $this->get('fos_user.util.token_generator');
                        $usuario->setConfirmationToken($tokenGenerator->generateToken());
                        $this->get('fos_user.mailer')->sendResettingEmailMessage($usuario);
                        $usuario->setPasswordRequestedAt(new \DateTime());
                        $this->get('fos_user.user_manager')->updateUser($usuario);
                        $em->persist($usuario);
                    }
                    
                }
            }
            
            // Comprobamos que el contacto existe
            $contacto = $entity->getSupervisor();
            
            // Revisamos si hubo cambio
            if($contacto !== $originalContacto)
            {
                // Sacamos al contacto Original
                $originalContacto->removePractica($entity);
                $em->persist($originalContacto);
                
                // Buscamos primero por email
                $contactoBuscado = $em->getRepository('pDevPracticasBundle:Contacto')->findOneByEmail($contacto->getEmail());
                
                // Si existe, lo añadimos
                if($contactoBuscado)
                {
                    $entity->setContacto($contactoBuscado);
                    $contactoBuscado->addPractica($entity);
                    $em->persist($contactoBuscado);
                }
                else
                {
                    // Creamos al contacto
                    $em->persist($contacto);
                    
                    // Revisamos si el correo / rut está asociado a un usuario.
                    $usuarioContacto = $em->getRepository('pDevUserBundle:User')->findOneByEmail($contacto->getEmail());
                    
                    // Probamos con el rut
                    if(!$usuarioContacto)
                        $usuarioContacto = $em->getRepository('pDevUserBundle:User')->findOneByRut($contacto->getRut()); 
                    
                    // Si lo encuentra, lo añade
                    if($usuarioContacto){
                        $contacto->setUsuario($usuarioContacto);
                    } else {
                        // Creamos el usuario
                        $userManager = $this->container->get('fos_user.user_manager');
                        $usuarioContacto = $userManager->createUser();
                        $usuarioContacto->setRut($contacto->getRut());
                        $usuarioContacto->setEmail($contacto->getEmail());
                        $usuarioContacto->setNombres($contacto->getNombres());
                        $usuarioContacto->setApellidoPaterno($contacto->getApellidoPaterno());
                        $usuarioContacto->setApellidoMaterno($contacto->getApellidoMaterno());
                        $usuarioContacto->setUsername($contacto->getEmail());
                        $usuarioContacto->setExternal(true);
                        $usuarioContacto->setEnabled(true);
                        
                        // Establecemos el usuario a este contacto
                        $contacto->setUsuario($usuarioContacto);
                        
                        // Seteamos la contraseña
                        $password = $usuarioContacto->getPassword();
                        $usuarioContacto->setSalt(md5(time()));
                        $factory = $this->get('security.encoder_factory');
                        $encoder = $factory->getEncoder($usuarioContacto);
                        $password = $encoder->encodePassword($usuarioContacto->getPassword(), $usuarioContacto->getSalt());
                        $usuarioContacto->setPassword($password);
                        $usuarioContacto->addRole("ROLE_USER");
                        
                        // Creamos el usuario y mandamos el mail
                        $tokenGenerator = $this->get('fos_user.util.token_generator');
                        $usuarioContacto->setConfirmationToken($tokenGenerator->generateToken());
                        $this->get('fos_user.mailer')->sendResettingEmailMessage($usuarioContacto);
                        $usuarioContacto->setPasswordRequestedAt(new \DateTime());
                        $this->get('fos_user.user_manager')->updateUser($usuarioContacto);
                        $em->persist($usuarioContacto);
                    }
                }
            }
            
            $em->persist($entity);
            $em->flush();
            
            $request->getSession()->getFlashBag()->add(
                'notice',
                'La práctica ha sido actualizada'
            );
    
            return $this->redirect($this->generateUrl('practicas_show', array('id' => $id)));
        }
        
        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
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
        // Chequeamos que sea el coordinador
        $pm = $this->get('permission.manager');
        $isCoordinador = $pm->isGranted("ROLE_ADMIN","SITE_PRACTICAS");
        
        if(!$isCoordinador){
            return $this->redirect($this->generateUrl('practicas_show', array('id' => $id)));
        }
        
        // Cargamos la entidad
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

            if ($editForm->isValid()) 
            {
                // Comprobamos si es correcto el estado
                if($entity->getEstado() == "estado.aprobada" && $entity->getTipo() == "")
                {
                    // Mensaje
                    $request->getSession()->getFlashBag()->add(
                        'error',
                        'Debe seleccionar el tipo de práctica'
                    );
                
                    return array(
                        'entity'      => $entity,
                        'edit_form'   => $editForm->createView(),
                    );
                }
                
                $em->persist($entity);
                $em->flush();
                
                // Mensaje
                $request->getSession()->getFlashBag()->add(
                    'notice',
                    'La práctica ha sido '.$entity->getEstadoLabel()
                );
                
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
        $isCoordinacion = $pm->isGranted("ROLE_ADMIN","SITE_PRACTICAS");
        
        // Cargamos la entidad
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('pDevPracticasBundle:Practica')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Practica entity.');
        }
        
        // Revisamos que sea el coordinador o el contacto
        if(!$isCoordinacion && !$entity->hasContacto($this->getUser()->getPersona('TYPE_PRACTICAS_CONTACTO'))){
            return $this->redirect($this->generateUrl('practicas_show', array('id' => $id)));
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
        // Permisos
        $pm = $this->get('permission.manager');
        $isCoordinacion = $pm->isGranted("ROLE_ADMIN","SITE_PRACTICAS");
        
        $form = $this->createDeleteForm($id);
        $form->submit($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('pDevPracticasBundle:Practica')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Practica entity.');
            }
            
            // Revisamos que sea el coordinador o el contacto
            if(!$isCoordinacion && !$entity->hasContacto($this->getUser()->getPersona('TYPE_PRACTICAS_CONTACTO'))){
                return $this->redirect($this->generateUrl('practicas_show', array('id' => $id)));
            }
        
            $em->remove($entity);
            $em->flush();
            
            $request->getSession()->getFlashBag()->add(
                'notice',
                'La práctica ha sido eliminada'
            );
            
            // Devolvemos la respuesta
            $array = array('redirect' => $this->generateUrl('practicas')); // data to return via JSON
            $response = new Response( json_encode($array));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
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
