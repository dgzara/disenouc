<?php

namespace pDev\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use pDev\UserBundle\Entity\User;
use pDev\UserBundle\Entity\Funcionario;
use pDev\UserBundle\Entity\Notificacion;
use pDev\UserBundle\Entity\Profesor;
use pDev\UserBundle\Form\PersonaType;
use pDev\UserBundle\Form\PersonaEditType;
use pDev\UserBundle\Form\PersonaEmailType;

/**
 * Persona controller.
 *
 * @Route("/personas")
 */
class PersonaController extends Controller
{

    /**
     * Lists all Persona entities.
     *
     * @Route("/todas", name="persona")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $pm = $this->get("permission.manager");
        $pm->isGrantedForbidden('ROLE_SUPER_USER',"SITE_PERSONAS");
        
        return $this->redirect($this->generateUrl('persona_page',array('page'=>1,'orderBy'=>'nombres','order'=>'asc')));   
    }
    
    /**
     * Lists all Persona entities.
     *
     * @Route("/todas/buscar", name="persona_buscar")
     * @Method("POST")
     * @Template("pDevUserBundle:Persona:index.html.twig")
     */
    public function indexBuscarAction()
    {
        $pm = $this->get("permission.manager");
        $pm->isGrantedForbidden('ROLE_SUPER_USER',"SITE_PERSONAS");
        
        $sh = $this->get("search.helper");
        $searchform = $this->createSearchPersonasForm();
        
        $request = $this->getRequest();
        
        $searchform->bind($request);

        if ($searchform->isValid())
        {
            $query = ((string)$searchform['querystring']->getData());                        
            $entitystring = 'pDevUserBundle:Persona';            
            
            // preparamos la consulta
            $em = $this->getDoctrine()->getManager();
            $qb = $em->getRepository($entitystring)->createQueryBuilder('p');
            $qb = $qb->select('p');
            
            $totalcount = $sh->getEntitiesCount($entitystring);
            $fields = $sh->getPersonaFields();
            $results = $sh->getResultados($fields,$query,$qb);
            
            return array(
                'entities' => $results,
                'total' => $totalcount,
                'search_form'=>$searchform->createView(),
                'anterior'=>false,
                'siguiente'=>false

            );
        }
            
        $nm = $this->get("notification.manager");
        $nm->createNotificacion('Ocurrió un error, inténtelo más tarde.',
                                            Notificacion::USER_ERROR
                                            );
        return $this->redirect($this->generateUrl('persona_page',array('page'=>1,'orderBy'=>'nombres','order'=>'asc')));
    }
    
    /** 
     * Lists all Persona entities.
     *
     * @Route("/todas/{page}/{orderBy}/{order}", name="persona_page")
     * @Method("GET")
     * @Template("pDevUserBundle:Persona:index.html.twig")
     */
    public function indexPageAction($page,$orderBy,$order)
    {
        $pm = $this->get("permission.manager");
        $pm->isGrantedForbidden('ROLE_SUPER_USER',"SITE_PERSONAS");
        
        $em = $this->getDoctrine()->getManager();
        
        if($orderBy!='nombres' and $orderBy!='apellidoPaterno' and $orderBy!='apellidoMaterno' and $orderBy!='email')
            throw $this->createNotFoundException();
        if($order!='asc' and $order!='desc')
            throw $this->createNotFoundException();
        
        $page = intval($page);
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $sh = $this->get("search.helper");
        $searchform = $this->createSearchPersonasForm('Buscar personas');
        
        $qb = $em->getRepository('pDevUserBundle:Persona')->createQueryBuilder('p');
        
        $count = $qb->select('COUNT(p)')
                    ->getQuery()
                    ->getSingleScalarResult();
                
        $anterior = $offset>0?$page-1:false;
        $siguiente = $page*$limit<$count?$page + 1:false;
        
        if($offset>$count or $page < 1)
        {
            throw $this->createNotFoundException();
        }
        
        $results = $qb->select('p')
                    ->orderBy('p.'.$orderBy, $order)
                    ->setFirstResult( $offset )
                    ->setMaxResults( $limit )
                    ->getQuery()
                    ->getResult();
        
        return array(
            'entities' => $results,
            'total' => $count,
            'search_form'=>$searchform->createView(),
            'anterior'=>$anterior,
            'siguiente'=>$siguiente
        );
    }
    
    /**
     * Lists all Alumnos entities.
     *
     * @Route("/alumnos/todos", name="persona_alumnos")
     * @Method("GET")
     
     */
    public function alumnosAction()
    {
        $pm = $this->get("permission.manager");
        $pm->isGrantedForbidden('ROLE_SUPER_USER',"SITE_ALUMNOS");
        
        return $this->redirect($this->generateUrl('persona_alumnos_page',array('periodo'=>'todos','page'=>1,'orderBy'=>'nombres','order'=>'asc')));
    }
    
    /**
     * Lists all Alumnos entities.
     *
     * @Route("/alumnos/buscar", name="persona_alumnos_buscar")
     * @Method("POST")
     * @Template("pDevUserBundle:Persona:alumnos.html.twig")
     */
    public function alumnosBuscarAction()
    {
        $pm = $this->get("permission.manager");
        $pm->isGrantedForbidden('ROLE_SUPER_USER',"SITE_ALUMNOS");
                
        $sh = $this->get("search.helper");
        $searchform = $this->createSearchPersonasForm('Buscar alumnos', 'números de alumno');
        $request = $this->getRequest();
        
        if ($request->isMethod('POST'))
        {
            $searchform->bind($request);
            
            if ($searchform->isValid())
            {
                $query = ((string)$searchform['querystring']->getData());                        
                $entitystring = 'pDevUserBundle:Alumno';            

                // preparamos la consulta
                $em = $this->getDoctrine()->getManager();
                $qb = $em->getRepository($entitystring)->createQueryBuilder('p');
                $qb = $qb->select('p');

                $totalcount = $sh->getEntitiesCount($entitystring);
                $fields = $sh->getPersonaFields(array('p.numeroAlumno'));
                $results = $sh->getResultados($fields,$query,$qb);

                return array(
                    'alumnos' => $results,
                    'total' => $totalcount,
                    'search_form'=>$searchform->createView(),
                    'anterior'=>false,
                    'siguiente'=>false
                        
                );
            }
            
        }
        
        $nm = $this->get("notification.manager");
        $nm->createNotificacion('Ocurrió un error, inténtelo más tarde.',
                                            Notificacion::USER_ERROR
                                            );
        return $this->redirect($this->generateUrl('persona_alumnos_page',array('page'=>1,'orderBy'=>'nombres','order'=>'asc')));
    }
    
    /**
     * Lists all Alumnos entities.
     *
     * @Route("/alumnos/todos/{periodo}/{page}/{orderBy}/{order}", name="persona_alumnos_page")
     * @Template("pDevUserBundle:Persona:alumnos.html.twig")
     */
    public function alumnosPageAction($periodo = null,$page = null,$orderBy = null,$order = null)
    {
        $pm = $this->get("permission.manager");
        $pm->isGrantedForbidden('ROLE_SUPER_USER',"SITE_ALUMNOS");
        
        $user = $pm->getUser();
        $em = $this->getDoctrine()->getManager();
        
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
            
            return $this->redirect($this->generateUrl('persona_alumnos_page',array('periodo'=>$periodo)));
        }
        
        $periodo2 = explode('-', $periodo);
                                
        if(count($periodo2)==2)
        {
            $year = intval($periodo2[0]);
            $semestre = intval($periodo2[1]);
            $periodo = $year.'-'.$semestre;
        }
        
        $periodo_form = $this->createPeriodForm($periodo);
        
        if(!$page or !$orderBy or !$order)
        {
            return $this->redirect($this->generateUrl('persona_alumnos_page',array('periodo'=>$periodo,'page'=>1,'orderBy'=>'nombres','order'=>'asc')));
        }
        
        if($orderBy!='nombres' and $orderBy!='apellidoPaterno' and $orderBy!='apellidoMaterno' and $orderBy!='email')
            throw $this->createNotFoundException();
        if($order!='asc' and $order!='desc')
            throw $this->createNotFoundException();
        
       $excel = null;
        if($page ==='excel')
        {
            $page = 1;
            $excel = true;
        }
        
        $page = intval($page);
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $sh = $this->get("search.helper");
        $searchform = $this->createSearchPersonasForm('Buscar alumnos','números de alumno');
        
        $qb = $em->getRepository('pDevUserBundle:Alumno')->createQueryBuilder('p');
        $count = $qb->select('COUNT(p)')
                    ->getQuery()
                    ->getSingleScalarResult();
        
        $anterior = $offset>0?$page-1:false;
        $siguiente = $page*$limit<$count?$page + 1:false;
        
        if($offset>$count or $page < 1)
        {
            throw $this->createNotFoundException();
        }
        
        $results = $qb->select('p');
        
        if($periodo !== 'todos')
        {
            $results = $results->leftJoin('p.periodos','periodo')
                       ->where('periodo.semestre = :semestre and periodo.year = :year')
                       ->setParameter('semestre',$semestre)
                       ->setParameter('year',$year);
        }
        
        
        
        if(!$excel)
            {
                $results = $results->orderBy('p.'.$orderBy, $order)
                    ->setFirstResult( $offset )
                    ->setMaxResults( $limit )
                    ->getQuery()
                    ->getResult();
            }
            else
            {
                $entities = $results->orderBy('p.'.$orderBy, $order)                    
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
                $excelService->excelObj->getActiveSheet()->setCellValueByColumnAndRow($ec,$ef,'EMAIL UC');
                $ec++;
                
                $ef++;
                $ec = 0;    
                foreach($entities as $entity)
                {
                    $excelService->excelObj->getActiveSheet()->setCellValueByColumnAndRow($ec,$ef,$entity->getNombreCompleto());
                    $ec++;
                    $excelService->excelObj->getActiveSheet()->setCellValueByColumnAndRow($ec,$ef,$entity->getNumeroAlumno());
                    $ec++;
                    $excelService->excelObj->getActiveSheet()->setCellValueByColumnAndRow($ec,$ef,$entity->getEmail());
                    
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
            'alumnos' => $results,
            'period_form'=>$periodo_form->createView(),
            'total' => $count,
            'search_form'=>$searchform->createView(),
            'anterior'=>$anterior,
            'siguiente'=>$siguiente
        );
    }
    
    /**
     * Lists all Profesores entities.
     *
     * @Route("/profesores", name="persona_profesores")
     * @Method("GET")
     * @Template()
     */
    public function profesoresAction()
    {
        $pm = $this->get("permission.manager");
        $pm->isGrantedForbidden('ROLE_SUPER_USER',"SITE_PERSONAS");
        
        return $this->redirect($this->generateUrl('persona_profesores_page',array('page'=>1,'orderBy'=>'nombres','order'=>'asc')));
    }
    
    /**
     * Lists all Persona entities.
     *
     * @Route("/profesores/buscar", name="persona_profesores_buscar")
     * @Method("POST")
     * @Template("pDevUserBundle:Persona:profesores.html.twig")
     */
    public function profesoresBuscarAction()
    {
        
        $pm = $this->get("permission.manager");
        $pm->isGrantedForbidden('ROLE_SUPER_USER',"SITE_PERSONAS");
        
        $sh = $this->get("search.helper");
        $searchform = $this->createSearchPersonasForm('Buscar académicos');
        $request = $this->getRequest();
        
        $searchform->bind($request);

        if ($searchform->isValid())
        {
            $query = ((string)$searchform['querystring']->getData());                        
            $entitystring = 'pDevUserBundle:Profesor';            
            
            // preparamos la consulta
            $em = $this->getDoctrine()->getManager();
            $qb = $em->getRepository($entitystring)->createQueryBuilder('p');
            $qb = $qb->select('p');
            
            $totalcount = $sh->getEntitiesCount($entitystring);
            $fields = $sh->getPersonaFields();
            $results = $sh->getResultados($fields,$query,$qb);
            
            $profesoressinuc = $em->getRepository('pDevUserBundle:Persona')->findBy(array('tipo'=>'TYPE_ACADEMICO','email'=>null));

            $aliases = $em->getRepository('pDevUserBundle:ProfesorAlias')->findByProfesor(null);

            return array(
                'profesores' => $results,
                'profesoressinuc' => $profesoressinuc,
                'aliases' => $aliases,
                'total' => $totalcount,
                'search_form'=>$searchform->createView(),
                'anterior'=>false,
                'siguiente'=>false

            );
        }
            
        $nm = $this->get("notification.manager");
        $nm->createNotificacion('Ocurrió un error, inténtelo más tarde.',
                                            Notificacion::USER_ERROR
                                            );
        
        return $this->redirect($this->generateUrl('persona_profesores_page',array('page'=>1,'orderBy'=>'nombres','order'=>'asc')));
    }
     
    /**
     *  Lists all Profesores entities.
     *
     * @Route("/profesores/{page}/{orderBy}/{order}", name="persona_profesores_page")
     * @Method("GET")
     * @Template("pDevUserBundle:Persona:profesores.html.twig")
     */
    public function profesoresPageAction($page,$orderBy,$order)
    {
        $pm = $this->get("permission.manager");
        $pm->isGrantedForbidden('ROLE_SUPER_USER',"SITE_PERSONAS");
        
        $em = $this->getDoctrine()->getManager();

        if($orderBy!='nombres' and $orderBy!='apellidoPaterno' and $orderBy!='apellidoMaterno' and $orderBy!='email')
            throw $this->createNotFoundException();
        if($order!='asc' and $order!='desc')
            throw $this->createNotFoundException();
        
        $page = intval($page);
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $sh = $this->get("search.helper");
        $searchform = $this->createSearchPersonasForm('Buscar académicos');
        
        $qb = $em->getRepository('pDevUserBundle:Profesor')->createQueryBuilder('p');
        $count = $qb->select('COUNT(p)')
                    ->getQuery()
                    ->getSingleScalarResult();
        
        $anterior = $offset>0?$page-1:false;
        $siguiente = $page*$limit<$count?$page + 1:false;
        
        if($offset>$count or $page < 1)
        {
            throw $this->createNotFoundException();
        }
        
        $results = $qb->select('p')
                    ->orderBy('p.'.$orderBy, $order)
                    ->setFirstResult( $offset )
                    ->setMaxResults( $limit )
                    ->getQuery()
                    ->getResult();
        
        $profesoressinuc = $em->getRepository('pDevUserBundle:Persona')->findBy(array('tipo'=>'TYPE_ACADEMICO','email'=>null));
        
        $aliases = $em->getRepository('pDevUserBundle:ProfesorAlias')->findByProfesor(null);

        return array(
            'profesores' => $results,
            'profesoressinuc' => $profesoressinuc,
            'aliases' => $aliases,
            'total' => $count,
            'search_form'=>$searchform->createView(),
            'anterior'=>$anterior,
            'siguiente'=>$siguiente
        );
        
        
        
        
    }
    
    /**
     * Lists all Funcionarios entities.
     *
     * @Route("/funcionarios", name="persona_funcionarios")
     * @Method("GET")
     * @Template()
     */
    public function funcionariosAction()
    {
        $pm = $this->get("permission.manager");
        $pm->isGrantedForbidden('ROLE_SUPER_USER',"SITE_PERSONAS");
        
        return $this->redirect($this->generateUrl('persona_funcionarios_page',array('page'=>1,'orderBy'=>'nombres','order'=>'asc')));
    }
    
    /**
     * Lists all Persona entities.
     *
     * @Route("/funcionarios/buscar", name="persona_funcionarios_buscar")
     * @Method("POST")
     * @Template("pDevUserBundle:Persona:funcionarios.html.twig")
     */
    public function funcionariosBuscarAction()
    {
        
        $pm = $this->get("permission.manager");
        $pm->isGrantedForbidden('ROLE_SUPER_USER',"SITE_PERSONAS");
        
        $sh = $this->get("search.helper");
        $searchform = $this->createSearchPersonasForm('Buscar funcionarios');
        $request = $this->getRequest();
        
        $searchform->bind($request);
            
        if ($searchform->isValid())
        {
            $query = ((string)$searchform['querystring']->getData());                        
            $entitystring = 'pDevUserBundle:Funcionario';            
            
            // preparamos la consulta
            $em = $this->getDoctrine()->getManager();
            $qb = $em->getRepository($entitystring)->createQueryBuilder('p');
            $qb = $qb->select('p');
            
            $totalcount = $sh->getEntitiesCount($entitystring);
            $fields = $sh->getPersonaFields();
            $results = $sh->getResultados($fields,$query,$qb);

            return array(
                'funcionarios' => $results,
                'total' => $totalcount,
                'search_form'=>$searchform->createView(),
                'anterior'=>false,
                'siguiente'=>false

            );
        }
        
        $nm = $this->get("notification.manager");
        $nm->createNotificacion('Ocurrió un error, inténtelo más tarde.',
                                            Notificacion::USER_ERROR
                                            );
        
        return $this->redirect($this->generateUrl('persona_funcionarios_page',array('page'=>1,'orderBy'=>'nombres','order'=>'asc')));
            
    }
    
    /**
     *  Lists all funcionarios entities.
     *
     * @Route("/funcionarios/{page}/{orderBy}/{order}", name="persona_funcionarios_page")
     * @Method("GET")
     * @Template("pDevUserBundle:Persona:funcionarios.html.twig")
     */
    public function funcionariosPageAction($page,$orderBy,$order)
    {
        $pm = $this->get("permission.manager");
        $pm->isGrantedForbidden('ROLE_SUPER_USER',"SITE_PERSONAS");
        
        $em = $this->getDoctrine()->getManager();

        if($orderBy!='nombres' and $orderBy!='apellidoPaterno' and $orderBy!='apellidoMaterno' and $orderBy!='email')
            throw $this->createNotFoundException();
        if($order!='asc' and $order!='desc')
            throw $this->createNotFoundException();
        
        $page = intval($page);
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $sh = $this->get("search.helper");
        $searchform = $this->createSearchPersonasForm('Buscar funcionarios');
        
        $qb = $em->getRepository('pDevUserBundle:Funcionario')->createQueryBuilder('p');
        $count = $qb->select('COUNT(p)')
                    ->getQuery()
                    ->getSingleScalarResult();
        $anterior = ($page - 1)*$limit>0?$page-1:false;
        $siguiente = ($page + 1)*$limit<$count?$page + 1:false;
        
        if($offset>$count or $page < 1)
        {
            throw $this->createNotFoundException();
        }
        
        $results = $qb->select('p')
                    ->orderBy('p.'.$orderBy, $order)
                    ->setFirstResult( $offset )
                    ->setMaxResults( $limit )
                    ->getQuery()
                    ->getResult();
        
        return array(
            'funcionarios' => $results,
            'total' => $count,
            'search_form'=>$searchform->createView(),
            'anterior'=>$anterior,
            'siguiente'=>$siguiente
        );
        
    }
    
    /**
     * Creates a new Persona entity.
     *
     * @Route("/{tipo}/create", name="persona_create")
     * @Method("POST")
     */
    public function createAction(Request $request,$tipo)
    {
        $pm = $this->get("permission.manager");
        $pm->isGrantedForbidden('ROLE_ADMIN',"SITE_PERSONAS");
        
        $persona = null;
        
        if($tipo=="funcionarios")
        {
            $persona = new Funcionario();
        }
        elseif($tipo=="profesores")
        {
            $persona = new Profesor();
        }
        else
            throw $this->createNotFoundException('Unable to find Persona entity.');
            
        $form = $this->createForm(new PersonaType(), $persona);
        $form->bind($request);
        $nm = $this->get("notification.manager");

        if ($form->isValid()) 
        {
            $em = $this->getDoctrine()->getManager();
            $ch = $this->get("context.helper");
            $mailuc = $ch->parseEmail($persona->getEmail(),array('uc.cl','puc.cl'));
            
            $mailalt = $persona->getEmailSecundario();
            $mailaltisset = isset($mailalt)?true:false;            
            $mailalt = $ch->parseEmail($mailalt);
            
            if($mailuc)
            {
                $persona->setEmail($mailuc);
                
                if($mailaltisset)
                {
                    if($mailalt)
                        $persona->setEmailSecundario($mailalt);
                    else
                    {
                        $mailaltisset = false;
                        
                        $nm->createNotificacion('Email alternativo no válido.',
                                            Notificacion::USER_ERROR
                                            );
                    }
                }
                else
                    $mailaltisset = true;
                
                if($mailaltisset)
                {
                    $em->persist($persona);
                    $em->flush();

                    $nm->createNotificacion('La operación se realizó con éxito.',
                                            Notificacion::USER_SUCCESS
                                            );
                }
            }
            else 
            {
                $nm->createNotificacion('Email UC no válido.',
                                            Notificacion::USER_ERROR
                                            );
            }
        }
        else
        {
            $nm->createNotificacion('Ocurrio un error, reintente más tarde.',
                                            Notificacion::USER_ERROR
                                            );
        }

        return $this->redirect($this->generateUrl('persona_'.$tipo));
    }

    /**
     * Displays a form to create a new Persona entity.
     *
     * @Route("/{tipo}/new", name="persona_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction($tipo)
    {
        $pm = $this->get("permission.manager");
        $pm->isGrantedForbidden('ROLE_ADMIN',"SITE_PERSONAS");
        
        $persona = null;
        
        if($tipo=="funcionarios")
        {
            $persona = new Funcionario();
        }
        elseif($tipo=="profesores")
        {
            $persona = new Profesor();
        }
        else
            throw $this->createNotFoundException('Unable to find Persona entity.');
        
        $form   = $this->createForm(new PersonaType(), $persona);

        return array(
            'form'   => $form->createView(),
            'tipo'  => $tipo
        );
    }
    
    /**
     * Displays a form to create a new Persona entity.
     *
     * @Route("/createuser/{mailuc}", name="persona_createuser")
     * @Method("GET")
     * @Template()
     */
    public function createUserAction($mailuc)
    {
        $pm = $this->get("permission.manager");
        $pm->isGrantedForbidden('ROLE_ADMIN',"SITE_PERSONAS");
        
        $em = $this->getDoctrine()->getManager();       
        
        $user = $em->getRepository('pDevUserBundle:User')->findOneByEmail($mailuc);
        if(!$user)
            $user = new User();
        $explodedmail = explode('@',$mailuc);
        $username = $explodedmail[0];
        $password = $username.'_username_test';
        
        $user->setUsername($username);        
        $user->setPlainPassword($password);
        $user->setEmail($mailuc);
        $user->setEnabled(true);
        
        $personas = $em->getRepository('pDevUserBundle:Persona')->findByEmail($mailuc);
        if(!$personas)
        {
            throw $this->createNotFoundException('Unable to find personas with mail: '.$mailuc);
        }
        
        foreach($personas as $persona)
        {
            if(!$user->getPersonas()->contains($persona))
            {
                $user->addPersona($persona);
                $persona->setUsuario($user);
            }
        }
        
        /*if(!$user->hasRole('ROLE_SUPER_ADMIN'))
            $user->addRole('ROLE_SUPER_ADMIN');
        */
        $em->persist($user);
        $em->flush();
        
        
        $this->get("permission.manager")->createPermisos($user);
        
        $nm = $this->get("notification.manager");
        $nm->createNotificacion('El usuario se ha creado con éxito.',
                                            Notificacion::USER_SUCCESS
                                            );
        
        return $this->redirect($this->generateUrl('user'));
    }

    /**
     * Finds and displays a Persona entity.
     *
     * @Route("/{tipo}/{id}", name="persona_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id,$tipo = 'auto')
    {
        $pm = $this->get("permission.manager");
        $pm->isGrantedForbidden('ROLE_SUPER_USER',"SITE_PERSONAS");
        
        $user = $pm->getUser();
        
        $isAlumno = false;
        $isSupervisor = false;
        $isContacto = false;        
        $isAcademico = false;
        
        $em = $this->getDoctrine()->getManager();

        $persona = $em->getRepository('pDevUserBundle:Persona')->find($id);

        if (!$persona) {
            throw $this->createNotFoundException('Unable to find Persona entity.');
        }
        
        if($tipo === 'alumno' and $persona->getTipo()==='TYPE_ALUMNO')
            $isAlumno = true;
        elseif($tipo === 'supervisor' and $persona->getTipo()==='TYPE_PRACTICAS_SUPERVISOR')
            $isSupervisor = true;
        elseif($tipo === 'contacto' and $persona->getTipo()==='TYPE_PRACTICAS_CONTACTO')
            $isContacto = true;
        elseif($tipo === 'academico' and $persona->getTipo()==='TYPE_ACADEMICO')
            $isAcademico = true;
        else
        {
            $tipo = $persona->getTipo();
            if($tipo ==='TYPE_ALUMNO')
                $tipo = 'alumno';
            elseif($tipo ==='TYPE_PRACTICAS_SUPERVISOR')
                $tipo = 'supervisor';
            elseif($tipo ==='TYPE_PRACTICAS_CONTACTO')
                $tipo = 'contacto';
            elseif($tipo ==='TYPE_ACADEMICO')
                $tipo = 'academico';
            
            return $this->redirect($this->generateUrl('persona_show',array('id'=>$id,'tipo'=>$tipo)));
        }
        
        $repo = $em->getRepository('pDevPracticasBundle:AlumnoPracticante');
        
        $practicantes = array();
        $organizaciones = null;
        if($isAlumno)
        {
            //planes de practica de alumno            
            $practicantes = $repo->createQueryBuilder('p')
                    ->leftJoin('p.alumno','a')
                    ->where('a.id = :idAlumno')
                    ->setParameter('idAlumno',$persona->getId())
                    ->getQuery()
                    ->getResult();
        }
        
        elseif($isSupervisor)
        {
            //planes de practica donde es supervisor
            
            $practicantes_repo = $repo->createQueryBuilder('p')
                    ->leftJoin('p.supervisor','s')
                    ->where('s.id = :id')
                    ->setParameter('id',$persona->getId())
                    ->getQuery()
                    ->getResult();
            $practicantes = $practicantes_repo;
            
            $organizaciones = array();
            foreach($practicantes as $practicante)
            {
                if(!in_array($practicante->getOrganizacionAlias()->getOrganizacion(),$organizaciones))
                    $organizaciones[] = $practicante->getOrganizacionAlias()->getOrganizacion();
            }
            
            
        }
        
        elseif($isContacto)
        {
            // de las organizaciones
            
            $practicantes_repo = $repo->createQueryBuilder('p')
                    ->leftJoin('p.organizacionAlias','oa')
                    //->leftJoin('oa.organizacion','o')
                    ->leftJoin('oa.practicas','pr')
                    ->leftJoin('pr.contacto','c')
                    ->where('c.id = :id')
                    ->setParameter('id',$persona->getId())
                    ->getQuery()
                    ->getResult();
            $practicantes = $practicantes_repo;
            
            
            
            $qb = $em->getRepository('pDevPracticasBundle:Practica')->createQueryBuilder('p');
            $practicas = $qb->leftJoin('p.organizacionAlias','oa')
                    ->leftJoin('oa.practicas','pr')
                    ->leftJoin('pr.contacto','c')
                    ->where('c.id = :id')
                    ->setParameter('id',$persona->getId())
                    ->getQuery()
                    ->getResult();
            
            $organizaciones = array();
            foreach($practicas as $practica)
            {
                if(!in_array($practica->getOrganizacionAlias()->getOrganizacion(),$organizaciones))
                    $organizaciones[] = $practica->getOrganizacionAlias()->getOrganizacion();
            }
            
        }
        elseif($isAcademico)
        {
            // evaluador
            
            $practicantes_repo = $repo->createQueryBuilder('p')
                    ->leftJoin('p.profesor','s')
                    ->where('s.id = :id')
                    ->setParameter('id',$persona->getId())
                    ->getQuery()
                    ->getResult();
            $practicantes = $practicantes_repo;
            
            
        }
        
        
        

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $persona,
            'practicantes'=> $practicantes,
            'organizaciones'=> $organizaciones,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Persona entity.
     *
     * @Route("/{tipo}/{idPersona}/email", name="persona_email")
     * @Template()
     */
    public function emailAction($idPersona,$tipo)
    {
        $pm = $this->get("permission.manager");
        $pm->isGrantedForbidden('ROLE_ADMIN',"SITE_PERSONAS");
        
        $em = $this->getDoctrine()->getManager();

        $persona = $em->getRepository('pDevUserBundle:Persona')->find($idPersona);

        if (!$persona) {
            throw $this->createNotFoundException('Unable to find Persona entity.');
        }
        
        $form = $this->createForm(new PersonaEmailType(), $persona);
        $request = $this->getRequest();
        
        if ($request->isMethod('POST'))
        {
            $form->bind($request);
            $nm = $this->get("notification.manager");

            if ($form->isValid()) {
                $ch = $this->get("context.helper");
                $mailuc = $ch->parseEmail($persona->getEmail(),array('uc.cl','puc.cl'));
            
                if($mailuc)
                {
                    $persona->setEmail($mailuc);
                    $em->persist($persona);
                    //$em->persist($user->getPermisos());
                    $em->flush();

                    $nm->createNotificacion('El email UC se ha modificado con éxito.',
                                            Notificacion::USER_SUCCESS
                                            );
                }
                else 
                {
                    $nm->createNotificacion('Email UC no válido.',
                                            Notificacion::USER_ERROR
                                            );
                }

            }
            else
            {
                $nm->createNotificacion('Ocurrió un error, intente más tarde.',
                                            Notificacion::USER_ERROR
                                            );
            }
            
            $redirect = "personas";
            if($tipo=="funcionarios")
            {
                $redirect = "persona_funcionarios";
            }
            elseif($tipo=="profesores")
            {
                $redirect = "persona_profesores";
            }

            return $this->redirect($this->generateUrl($redirect));
        }


        return array(
            'persona'      => $persona,
            'form'   => $form->createView(),
            'tipo' => $tipo
        );
    }
    
    /**
     * Displays a form to edit an existing Persona entity.
     *
     * @Route("/{tipo}/{idPersona}/edit", name="persona_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($idPersona,$tipo = 'auto')
    {
        $pm = $this->get("permission.manager");
        $pm->isGrantedForbidden('ROLE_ADMIN',"SITE_PERSONAS");
        
        $em = $this->getDoctrine()->getManager();

        $persona = $em->getRepository('pDevUserBundle:Persona')->find($idPersona);

        if (!$persona) {
            throw $this->createNotFoundException('Unable to find Persona entity.');
        }
        
        if($tipo ==='auto')
        {
            $tipo = $persona->getTipo();
            if($tipo ==='TYPE_ALUMNO')
                $tipo = 'alumno';
            elseif($tipo ==='TYPE_PRACTICAS_SUPERVISOR')
                $tipo = 'supervisor';
            elseif($tipo ==='TYPE_PRACTICAS_CONTACTO')
                $tipo = 'contacto';
            elseif($tipo ==='TYPE_ACADEMICO')
                $tipo = 'academico';
            
            return $this->redirect($this->generateUrl('persona_edit',array('idPersona'=>$idPersona,'tipo'=>$tipo)));
        }

        $editForm = $this->createForm(new PersonaEditType(), $persona);

        return array(
            'persona'      => $persona,
            'form'   => $editForm->createView(),
            'tipo' => $tipo
        );
    }

    /**
     * Edits an existing Persona entity.
     *
     * @Route("/{tipo}/{idPersona}/update", name="persona_update")
     * @Method("PUT")
     * @Template("pDevUserBundle:Persona:edit.html.twig")
     */
    public function updateAction(Request $request, $idPersona,$tipo)
    {
        $pm = $this->get("permission.manager");
        $pm->isGrantedForbidden('ROLE_ADMIN',"SITE_PERSONAS");
        
        $em = $this->getDoctrine()->getManager();

        $persona = $em->getRepository('pDevUserBundle:Persona')->find($idPersona);

        if (!$persona) {
            throw $this->createNotFoundException('Unable to find Persona entity.');
        }

        $editForm = $this->createForm(new PersonaEditType(), $persona);
        $editForm->bind($request);
        $nm = $this->get("notification.manager");
        
        if ($editForm->isValid()) {
            $ch = $this->get("context.helper");
            $mailalt = $persona->getEmailSecundario();
            $mailaltisset = isset($mailalt)?true:false;            
            $mailalt = $ch->parseEmail($mailalt);
            
            
            if($mailaltisset)
            {
                if($mailalt)
                    $persona->setEmailSecundario($mailalt);
                else
                {
                    $mailaltisset = false;

                    $nm->createNotificacion('Email alternativo no válido.',
                                            Notificacion::USER_ERROR
                                            );
                }
            }
            else
                $mailaltisset = true;
            
            if($mailaltisset)
            {
                $em->persist($persona);
                $em->flush();

                $nm->createNotificacion('La persona se ha modificado con éxito.',
                                            Notificacion::USER_SUCCESS
                                            );
            }
        }
        else
        {
            $nm->createNotificacion('Ocurrió un error, intente más tarde.',
                                            Notificacion::USER_ERROR
                                            );
        }
        
        $redirect = "personas";
        if($tipo=="funcionarios")
        {
            $redirect = "persona_funcionarios";
        }
        elseif($tipo=="profesores" or $tipo=="academico")
        {
            $redirect = "persona_profesores";
        }
        elseif($tipo=="supervisor")
        {
            $redirect = "practicas_supervisor";
        }
        elseif($tipo=="contacto")
        {
            $redirect = "personas_contacto";
        }
        elseif($tipo=="alumno")
        {
            $redirect = "personas_alumno";
        }

        return $this->redirect($this->generateUrl($redirect));
    }
    /**
     * Deletes a Persona entity.
     *
     * @Route("/{id}", name="persona_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $pm = $this->get("permission.manager");
        $pm->isGrantedForbidden('ROLE_ADMIN',"SITE_PERSONAS");
        
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('pDevUserBundle:Persona')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Persona entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('persona'));
    }

    /**
     * Creates a form to delete a Persona entity by id.
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
    
    private function createSearchPersonasForm($placeholder = 'Buscar personas',$tooltip=null)
    {
        $base = 'Por nombres, apellidos, RUT, direcciones de correo electrónico';
        if($tooltip)
            $tooltip = $base.', '.$tooltip;
        else
            $tooltip = $base;
        
        return $this->createSearchForm($placeholder,$tooltip);
    }
    
    private function createSearchForm($placeholder = 'Buscar',$tooltip=null)
    {
        $attr = array('placeholder'=>$placeholder);
        if($tooltip)
        {
            $attr['title']=$tooltip;
            $attr['data-toggle']='tooltip';
        }
        return $this->createFormBuilder()
            ->add('querystring', 'search',array('label'=>false,'attr' => $attr))
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
        
        $periodos = array('todos'=>'Todos');
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
