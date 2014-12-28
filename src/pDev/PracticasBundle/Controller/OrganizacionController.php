<?php

namespace pDev\PracticasBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use pDev\PracticasBundle\Entity\Organizacion;
use pDev\PracticasBundle\Entity\OrganizacionAlias;
use pDev\PracticasBundle\Form\OrganizacionType;
use pDev\PracticasBundle\Form\OrganizacionAliasType;

/**
 * Organizacion controller.
 *
 * @Route("/practicas/organizaciones")
 */
class OrganizacionController extends Controller
{

    /**
     * Lists all Organizacion entities.
     *
     * @Route("/todas/{page}/{orderBy}/{order}", name="practicas_organizacion")
     * @Method("GET")
     * @Template()
     */
    public function indexAction($page = null,$orderBy = null,$order = null)
    {
        $pm = $this->get("permission.manager");
        $user = $pm->getUser();
        $em = $this->getDoctrine()->getManager();

        $dql   = "SELECT o 
                  FROM pDevPracticasBundle:Organizacion o
                  LEFT JOIN o.aliases a";
        $query = $em->createQuery($dql);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1)/*page number*/,
            10/*limit per page*/
        );

        return array(
            'pagination' => $pagination,
        );
    }
    
    /**
     * Lists all Organizacion entities.
     *
     * @Route("/excel", name="practicas_excel")
     * @Method("GET")
     * @Template()
     */
    public function excelAction()
    {
        $pm = $this->get("permission.manager");
        $user = $pm->getUser();
        $em = $this->getDoctrine()->getManager();

        $entities = $results->orderBy($orderBy, $order)                    
            ->getQuery()
            ->getResult();
        $excelService = $this->get('xls.service_xls2007');

        $excelService->excelObj->getProperties()->setCreator($user->getNombrecompleto())
                            ->setTitle('Practicas')
                            ->setSubject('');

        $excelService->excelObj->setActiveSheetIndex(0);
        $ec = 0;
        $ef = 1;

        $excelService->excelObj->getActiveSheet()->setCellValueByColumnAndRow($ec,$ef,'NOMBRES');
        $ec++;
        $excelService->excelObj->getActiveSheet()->setCellValueByColumnAndRow($ec,$ef,'RUT');
        $ec++;
        $excelService->excelObj->getActiveSheet()->setCellValueByColumnAndRow($ec,$ef,'RUBRO');
        $ec++;
        $excelService->excelObj->getActiveSheet()->setCellValueByColumnAndRow($ec,$ef,'PAIS');
        $ec++;
        $excelService->excelObj->getActiveSheet()->setCellValueByColumnAndRow($ec,$ef,'WEB');
        $ec++;
        $excelService->excelObj->getActiveSheet()->setCellValueByColumnAndRow($ec,$ef,'ANTIGUEDAD');
        $ec++;
        $excelService->excelObj->getActiveSheet()->setCellValueByColumnAndRow($ec,$ef,'NUMERO PERSONAS');
        $ec++;
        
        $ef++;
        $ec = 0;    
        foreach($entities as $entity)
        {
            $nombres = '';
            foreach($entity->getAliases() as $alias)
            {
                $nombres .= $alias->getNombre().', ';
            }
            
            $excelService->excelObj->getActiveSheet()->setCellValueByColumnAndRow($ec,$ef,$nombres);
            $ec++;
            $excelService->excelObj->getActiveSheet()->setCellValueByColumnAndRow($ec,$ef,$entity->getRut());
            $ec++;
            $excelService->excelObj->getActiveSheet()->setCellValueByColumnAndRow($ec,$ef,$entity->getRubro());
            $ec++;
            $excelService->excelObj->getActiveSheet()->setCellValueByColumnAndRow($ec,$ef,$entity->getPais());
            $ec++;
            $excelService->excelObj->getActiveSheet()->setCellValueByColumnAndRow($ec,$ef,$entity->getWeb());
            $ec++;
            $excelService->excelObj->getActiveSheet()->setCellValueByColumnAndRow($ec,$ef,$entity->getAntiguedad());
            $ec++;
            $excelService->excelObj->getActiveSheet()->setCellValueByColumnAndRow($ec,$ef,$entity->getPersonasTotal());
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
     * Creates a new Organizacion entity.
     *
     * @Route("/", name="practicas_organizacion_create")
     * @Method("POST")
     * @Template("pDevPracticasBundle:Organizacion:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Organizacion();
        $form = $this->createForm(new OrganizacionType(), $entity);
        $form->submit($request);
        
        $organizacionAlias = new OrganizacionAlias();
        $organizacionAlias_form = $this->createForm(new OrganizacionAliasType(), $organizacionAlias);
        $organizacionAlias_form->submit($request);

        if ($form->isValid() and $organizacionAlias_form->isValid()) 
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            
            $organizacionAlias_tmp = $em->getRepository('pDevPracticasBundle:OrganizacionAlias')->findOneByNombre($organizacionAlias->getNombre());
            if($organizacionAlias_tmp)
            {
                $organizacionAlias_tmp->setOrganizacion ($entity);  
                $organizacionAlias = $organizacionAlias_tmp;
            }
            else
            {
                $organizacionAlias->setOrganizacion($entity);
                $em->persist($organizacionAlias);
            }
            $em->flush();

            return $this->redirect($this->generateUrl('practicas_organizacion_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Organizacion entity.
     *
     * @Route("/new", name="practicas_organizacion_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Organizacion();
        $form   = $this->createForm(new OrganizacionType(), $entity);
        
        $organizacionAlias = new OrganizacionAlias();
        $organizacionAlias_form = $this->createForm(new OrganizacionAliasType(), $organizacionAlias);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'organizacionAlias_form' => $organizacionAlias_form->createView()
        );
    }

    /**
     * Finds and displays a Organizacion entity.
     *
     * @Route("/{id}/show", name="practicas_organizacion_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('pDevPracticasBundle:Organizacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Organizacion entity.');
        }
        
        
        $supervisores = $em->getRepository('pDevPracticasBundle:Supervisor')->createQueryBuilder('p')
                ->leftJoin('p.practicantes','pr')
                ->leftJoin('pr.organizacionAlias','oa')
                ->leftJoin('oa.organizacion','o')
                ->where('o.id = :id')
                ->setParameter('id',$entity->getId())
                ->getQuery()
                ->getResult();
                   
        
        $contactos = $em->getRepository('pDevPracticasBundle:Contacto')->createQueryBuilder('p')
                ->leftJoin('p.practicas','pr')
                ->leftJoin('pr.organizacionAlias','oa')
                ->leftJoin('oa.organizacion','o')
                ->where('o.id = :id')
                ->setParameter('id',$entity->getId())
                ->getQuery()
                ->getResult();

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
            'supervisores'    => $supervisores,
            'contactos' => $contactos 
        );
    }

    /**
     * Displays a form to edit an existing Organizacion entity.
     *
     * @Route("/{id}/edit", name="practicas_organizacion_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('pDevPracticasBundle:Organizacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Organizacion entity.');
        }

        $editForm = $this->createForm(new OrganizacionType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Organizacion entity.
     *
     * @Route("/{id}", name="practicas_organizacion_update")
     * @Method("PUT")
     * @Template("pDevPracticasBundle:Organizacion:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('pDevPracticasBundle:Organizacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Organizacion entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new OrganizacionType(), $entity);
        $editForm->submit($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('practicas_organizacion_show', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Organizacion entity.
     *
     * @Route("/{id}", name="practicas_organizacion_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->submit($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('pDevPracticasBundle:Organizacion')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Organizacion entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('practicas_organizacion'));
    }
    
    /**
     * Displays a form to create a new Organizacion entity.
     *
     * @Route("/find", name="practicas_organizacion_find")
     * @Method("GET")
     */
    public function searchAction()
    {
        $return = array();
        
        $data = strtolower($this->get('request')->query->get('query'));

        $em = $this->getDoctrine()->getManager();


        $qb = $em->getRepository('pDevPracticasBundle:OrganizacionAlias')->createQueryBuilder('p');
        $entities = $qb
                        ->where('p.nombre like :nombre')
                        ->setParameter('nombre','%'.$data.'%')
                        ->getQuery()
                        ->getResult();


        foreach ($entities as $entity)
        {
            $return[] = array(
                'label'=> $entity->getNombre(),
                'value'=>$entity->getNombre(),
                'rut'=>$entity->getOrganizacion()->getRut(),
                'rubro'=>$entity->getOrganizacion()->getRubro(),
                'descripcion'=>$entity->getOrganizacion()->getDescripcion(),
                'pais'=>$entity->getOrganizacion()->getPais(),
                'web'=>$entity->getOrganizacion()->getWeb(),
                'personasTotal'=>$entity->getOrganizacion()->getPersonasTotal(),
                'antiguedad'=>$entity->getOrganizacion()->getAntiguedad(),
                );
        }
                 
        $response = new Response(json_encode($return));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    } 

    /**
     * Displays a form to create a new Organizacion entity.
     *
     * @Route("/lista.json", name="practicas_organizacion_json")
     * @Method("GET")
     */
    public function jsonAction()
    {
        $return = array();
        
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('pDevPracticasBundle:OrganizacionAlias')->findAll();

        foreach ($entities as $entity)
        {
            $return[] = array(
                'id' => $entity->getId(),
                'value' => $entity->getNombre(),
                'rut' => $entity->getOrganizacion()->getRut(),
                'rubro'=> $entity->getOrganizacion()->getRubro(),
                'descripcion' => $entity->getOrganizacion()->getDescripcion(),
                'pais' => $entity->getOrganizacion()->getPais(),
                'web' => $entity->getOrganizacion()->getWeb(),
            );
        }
                 
        $response = new Response(json_encode($return));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    } 
    
    /**
     * Creates a form to delete a Organizacion entity by id.
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
