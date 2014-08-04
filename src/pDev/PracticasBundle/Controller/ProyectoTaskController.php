<?php

namespace pDev\PracticasBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Doctrine\Common\Collections\ArrayCollection;

use pDev\PracticasBundle\Entity\ProyectoTask;

use pDev\PracticasBundle\Form\ProyectoTaskType;

/**
 * ProyectoTask controller.
 *
 * @Route("/practicas/tarea")
 */
class ProyectoTaskController extends Controller
{
    /**
     * Displays a form to edit an existing ProyectoTask entity.
     *
     * @Route("/proyecto/{idProyecto}/{idTarea}/edit", name="practicas_alumno_tarea", options={"expose"=true})
     * @Template()
     */
    public function tareaAction($idProyecto, $idTarea)
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
            'idProyecto'    => $idProyecto,
            'idTarea'       => $idTarea,
            'idPracticante' => $entity->getPracticante()->getId(),
            'edit_form'     => $editForm->createView(),
        );
    }
    
    /**
     * Displays a form to edit an existing AlumnoPracticante entity.
     *
     * @Route("/proyecto/{idProyecto}/{idTarea}/remove", name="practicas_alumno_tarea_remove", options={"expose"=true})
     * @Template()
     */
    public function tareaRemoveAction($idProyecto, $idTarea)
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
                if(count($entity->getTareas()) > 1)
                    $em->remove($tarea);
                
                $em->flush();
                
                $response = new Response(json_encode(array('status'=>'ok')));
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
        }
            
        return array(
            'idProyecto'    => $idProyecto,
            'idTarea'       => $idTarea,
            'idPracticante' => $entity->getPracticante()->getId(),
            'edit_form'     => $editForm->createView(),
        );
    }
}
