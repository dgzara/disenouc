<?php

namespace pDev\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use pDev\UserBundle\Entity\User;
use pDev\UserBundle\Form\UserFotoType;
use pDev\UserBundle\Form\UserBasicType;
use pDev\UserBundle\Form\UserPermisosType;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use pDev\UserBundle\Entity\Notificacion;

/**
 * User controller.
 *
 * @Route("/usuarios")
 */
class UserController extends Controller
{

    /**
     * Lists all User entities.
     *
     * @Route("/", name="user")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $pm = $this->get("permission.manager");
        $pm->isGrantedForbidden('ROLE_SUPER_USER',"SITE_PERMISOS");

        return $this->redirect($this->generateUrl('user_page',array('page'=>1)));
    }
    
    /**
     * Lists all Persona entities.
     *
     * @Route("/buscar", name="user_buscar")
     * @Method("POST")
     * @Template("pDevUserBundle:User:index.html.twig")
     */
    public function indexBuscarAction()
    {
        
        $pm = $this->get("permission.manager");
        $pm->isGrantedForbidden('ROLE_SUPER_USER',"SITE_PERMISOS");
        
        $searchform = $this->createSearchForm('Buscar usuarios','nombre de usuario');
        $request = $this->getRequest();
        
        $searchform->bind($request);

        if ($searchform->isValid())
        {
            $query = ((string)$searchform['querystring']->getData());
            $entitystring = 'pDevUserBundle:User';            
            
            // preparamos la consulta
            $em = $this->getDoctrine()->getManager();
            $qb = $em->getRepository($entitystring)->createQueryBuilder('u');
            $qb = $qb->select('u')
                     ->leftJoin('u.personas','p');
            
            $sh = $this->get("search.helper");
            $totalcount = $sh->getEntitiesCount($entitystring);
            $fields = $sh->getPersonaFields(array('u.username'));
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
        $nm->createNotificacion('Error', 'Ocurrió un error, inténtelo más tarde.',
                                            Notificacion::USER_ERROR
                                            );
        
        return $this->redirect($this->generateUrl('user_page',array('page'=>1,'orderBy'=>'nombres','order'=>'asc')));
    }
    
    /**
     * Lists all User entities.
     *
     * @Route("/{page}", name="user_page")
     * @Method("GET")
     * @Template("pDevUserBundle:User:index.html.twig")
     */
    public function indexPageAction($page)
    {
        $pm = $this->get("permission.manager");
        $pm->isGrantedForbidden('ROLE_SUPER_USER',"SITE_PERMISOS");
        
        $em = $this->getDoctrine()->getManager();
        
        $page = intval($page);
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        
        $searchform = $this->createSearchForm('Buscar usuarios','nombre de usuario');
        
        $qbcount = $em->getRepository('pDevUserBundle:User')->createQueryBuilder('p');
        $count = $qbcount->select('COUNT(p)')
                    ->getQuery()
                    ->getSingleScalarResult();
        
        $anterior = $offset>0?$page-1:false;
        $siguiente = $page*$limit<$count?$page + 1:false;
        
        if($offset>$count or $page < 1)
        {
            throw $this->createNotFoundException();
        }
        
        
        
        $qb = $em->getRepository('pDevUserBundle:User')->createQueryBuilder('p');
        $results = $qb->setFirstResult( $offset )
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
     * Creates a new User entity.
     *
     * @Route("/", name="user_create")
     * @Method("POST")
     * @Template("pDevUserBundle:User:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new User();
        $form = $this->createForm(new UserType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('user_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new User entity.
     *
     * @Route("/new", name="user_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new User();
        $form   = $this->createForm(new UserType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a User entity.
     *
     * @Route("/{id}", name="user_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('pDevUserBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }
    
    /**
     * Finds and displays a User entity.
     *
     * @Route("/{id}/modal", name="user_show_modal")
     * @Method("GET")
     * @Template()
     */
    public function showModalAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('pDevUserBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        return array(
            'entity'      => $entity,
        );
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     * @Route("/{username}/editFoto", name="user_edit")
     * @Method("GET")
     * @Template()
     */
    public function editFotoAction($username)
    {
        $pm = $this->get("permission.manager");
        $pm->throwForbidden(!$pm->checkUsername($username));
        
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('pDevUserBundle:User')->findOneByUsername($username);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $editForm = $this->createForm(new UserFotoType(), $entity);
        $deleteForm = $this->createDeleteForm($entity->getId());

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    
    /**
     * Displays a form to edit an existing User entity.
     *
     * @Route("/{username}/editbasic", name="user_edit_basic")
     * @Template()
     */
    public function editBasicAction($username)
    {
        $pm = $this->get("permission.manager");
        $pm->throwForbidden(!$pm->checkUsername($username));
        
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('pDevUserBundle:User')->findOneByUsername($username);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $editForm = $this->createForm(new UserBasicType(), $entity);
        $request = $this->getRequest();
        if($request->isMethod('PUT'))
        {
            $editForm->submit($request);
            if($editForm->isValid())
            {
                $em->persist($entity);
                $em->flush();
                
                return $this->redirect($this->generateUrl('default_inicio'));
            }
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),            
        );
    }
    
    /**
     * Edits an existing User entity.
     *
     * @Route("/{username}/updateFoto", name="user_update")
     * @Method("PUT")
     */
    public function updateFotoAction(Request $request, $username)
    {
        $pm = $this->get("permission.manager");
        $pm->throwForbidden(!$pm->checkUsername($username));
        
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('pDevUserBundle:User')->findOneByUsername($username);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $editForm = $this->createForm(new UserFotoType(), $entity);
        $editForm->bind($request);
        $nm = $this->get("notification.manager");

        if ($editForm->isValid()) {
            $em->persist($entity);
            $foto = $entity->getFoto();
            if(!$foto->getPath())
            {
                $entity->setFoto(null);
                
                $nm->createNotificacion('Error', 'Debe seleccionar un archivo de imagen.',
                                            Notificacion::USER_ERROR
                                            );
            }
            else
            {
                $foto->setOwner($entity);
                $em->persist($foto);
                
                $nm->createNotificacion('Imagen actualizada', 'Su imagen de perfil se ha cambiado con éxito.',
                                            Notificacion::USER_SUCCESS
                                            );
            }
            $em->flush();

        }
        else
        {
            $nm->createNotificacion('Error', 'Ocurrió un error, inténtelo más tarde.',
                                            Notificacion::USER_ERROR
                                            );
        }

        return $this->redirect($this->generateUrl('default_inicio'));
    }
    
    /**
     * Gets a Archivo entity.
     *
     * @Route("/{username}/foto", name="user_foto")
     * @Template()
     */
    public function getFotoAction($username)
    {
        $pm = $this->get("permission.manager");        
        $pm->throwForbidden(!$pm->checkUsername($username));
        
        $em = $this->getDoctrine()->getManager();
        
        $user = $em->getRepository('pDevUserBundle:User')->findOneByUsername($username);
        if (!$user) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }
        
        $foto = $user->getFoto();
        if(!$foto or !$foto->getAbsolutePath())
        {
            $path = "/img/noimage.png";
            return $this->redirect($path);            
        }
        
        $isOwner = $foto->getOwner()->getId() === $pm->getUser()->getId();
        //$isGranted = $pm->isGranted('ROLE_SUPER_USER',$documento->getSite()->getSite());
        
        $pm->throwForbidden(!$isOwner);
        
        $response = new BinaryFileResponse($foto->getAbsolutePath());
        $response->headers->set('Content-Type', $foto->getMimetype());
        //$response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $foto->getPath());
        return $response;
    }
    
    /**
     * Displays a form to edit an existing User entity.
     *
     * @Route("/{username}/permisos", name="user_permisos")
     * @Template()
     */
    public function permisosAction($username)
    {
        $pm = $this->get("permission.manager");
        $pm->isGrantedForbidden('ROLE_ADMIN',"SITE_PERMISOS");
        
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('pDevUserBundle:User')->findOneByUsername($username);

        if (!$user) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }
        
        $pm->createPermisos($user);

        $form = $this->createForm(new UserPermisosType(), $user);
        $request = $this->getRequest();
        
        if ($request->isMethod('POST'))
        {
            $form->bind($request);
            $nm = $this->get("notification.manager");

            if ($form->isValid()) {
                $em->persist($user);
                //$em->persist($user->getPermisos());
                $em->flush();
                
                $nm->createNotificacion('Permisos actualizados', 'Los permisos se han modificado con éxito.',
                                            Notificacion::USER_SUCCESS
                                            );

            }
            else
            {
                $nm->createNotificacion('Error', 'Ocurrió un error, inténtelo más tarde.',
                                            Notificacion::USER_ERROR
                                            );
            }

            return $this->redirect($this->generateUrl('user'));
        }

        return array(
            'user'      => $user,
            'form'   => $form->createView(),
        );
    }
    
    /**
     * Deletes a User entity.
     *
     * @Route("/{id}", name="user_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('pDevUserBundle:User')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find User entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('user'));
    }
    
    /**
     * Displays a form to create a new Organizacion entity.
     *
     * @Route("/active/data", name="user_data")
     * @Method("GET")
     */
    public function userDataAction()
    {
        $pm = $this->get("permission.manager");
        $user = $pm->getUser();
        
        $return = array();


        $return[] = array(
            'rut'=> $user->getRut(),
            'nombres'=>$user->getNombres(),
            'apellidoPaterno'=>$user->getApellidoPaterno(),
            'apellidoMaterno'=>$user->getApellidoMaterno(),
            'email'=>$user->getEmail(),
            'direccionCalle'=>$user->getDireccionCalle(),
            'numeroTelefono'=>$user->getNumeroTelefono()
            );
        
        
                 
        $response = new Response(json_encode($return));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    } 

    /**
     * Creates a form to delete a User entity by id.
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
    
    private function createSearchForm($placeholder = 'Buscar usuarios',$tooltip=null)
    {
        $base = 'Por nombres, apellidos, RUT, direcciones de correo electrónico';
        if($tooltip)
            $tooltip = $base.', '.$tooltip;
        else
            $tooltip = $base;
        
        return $this->createFormBuilder()
            ->add('querystring', 'search',array('label'=>false,'attr' => array('placeholder'=>$placeholder, 'data-toggle'=>'tooltip', 'title'=>$tooltip)))
            ->getForm()
        ;
    }
    
    
}
