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

        $entities = $em->getRepository('pDevPracticasBundle:ProfesorEvaluador')->findAll();

        return array(
            'entities' => $entities,
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
