<?php

namespace pDev\PracticasBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use pDev\PracticasBundle\Entity\ProfesorEvaluador;

/**
 * ProfesorEvaluador controller.
 *
 * @Route("/practicas/profesor")
 */
class ProfesorEvaluadorController extends Controller
{

    /**
     * Lists all ProfesorEvaluador entities.
     *
     * @Route("/", name="practicas_profesor")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $dql   = "SELECT p FROM pDevPracticasBundle:ProfesorEvaluador p";
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
     * Finds and displays a ProfesorEvaluador entity.
     *
     * @Route("/{id}", name="practicas_profesor_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('pDevPracticasBundle:ProfesorEvaluador')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ProfesorEvaluador entity.');
        }

        return array(
            'entity'      => $entity,
        );
    }
}
