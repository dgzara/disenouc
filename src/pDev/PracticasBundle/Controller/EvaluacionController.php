<?php

namespace pDev\PracticasBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use pDev\PracticasBundle\Entity\EvaluacionProfesor;
use pDev\PracticasBundle\Entity\EvaluacionSupervisor;
use pDev\PracticasBundle\Entity\AlumnoPracticante;
use pDev\PracticasBundle\Entity\Criterio;
use pDev\PracticasBundle\Entity\CriterioTipo;
use pDev\PracticasBundle\Form\EvaluacionProfesorType;
use pDev\PracticasBundle\Form\EvaluacionSupervisorType;
use pDev\PracticasBundle\Form\EvaluacionProfesorDescuentoType;

/**
 * Evaluacion controller.
 *
 * @Route("/practicas/evaluacion")
 */
class EvaluacionController extends Controller
{
    /**
     * Lists all Evaluacion entities.
     *
     * @Route("/", name="practicas_evaluacion")
     * @Template()
     */
    public function indexAction()
    {
        $pm = $this->get('permission.manager');
        $user = $pm->getUser();        
        $isCoordinacion = $pm->isGranted("ROLE_ADMIN","SITE_PRACTICAS");        
        $isAcademico = $pm->checkType("TYPE_ACADEMICO");
        $isSupervisor = $pm->checkType("TYPE_PRACTICAS_SUPERVISOR");
               
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('pDevPracticasBundle:AlumnoPracticante');
        $consulta = $repo->createQueryBuilder('p')
                    ->leftJoin('p.alumno','a')
                    ->leftJoin('a.periodos','periodo')
                    ->leftJoin('p.supervisor','s')
                    ->leftJoin('p.organizacion','o')                                        
                    ->leftJoin('p.profesor','prof')
                    ->leftJoin('p.profesorEvaluacion','evaluacion');
      
        if($isAcademico)
            $consulta->where('prof.id = :profesor')->setParameter('profesor', $user->getPersona('TYPE_ACADEMICO'));
        elseif($isSupervisor)
            $consulta->where('supervisor.id = :supervisor')->setParameter('supervisor', $user->getPersona('TYPE_PRACTICAS_SUPERVISOR'));
                    
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $consulta,
            $this->get('request')->query->get('page', 1)/*page number*/,
            10/*limit per page*/
        );
  
        return array(
            'pagination' => $pagination,    
            'isCoordinacion' => $isCoordinacion,
            'isAcademico' => $isAcademico, 
            'isSupervisor' => $isSupervisor   
        );
    }
}
