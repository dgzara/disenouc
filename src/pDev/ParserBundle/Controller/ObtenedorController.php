<?php

namespace pDev\ParserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Importa controller.
 *
 * @Route("/obtener")
 */
class ObtenedorController extends Controller
{

    /**
     * ////////////////////////////////////////////////////////////////////////////////////////////////////////////
     * ////////
     * ////////    Obtenedores
     * ////////
     * ////////////////////////////////////////////////////////////////////////////////////////////////////////////
    */
    
    /**
     * @Route("/foto/{rut}", name="obtener_foto")
     */
    public function getFotoAction($rut)
    {
        $ch = $this->get("context.helper");
        $rut = $ch->parseRut($rut);
        
        $pm = $this->get("permission.manager");
        if(!($pm->checkType("TYPE_ALUMNO") and $pm->getUser()->getPersona('TYPE_ALUMNO')->getRut()==$rut))
        {
            $isGranted = $pm->isGranted('ROLE_SUPER_USER',"SITE_ALUMNOS") || $pm->isGranted('ROLE_SUPER_USER',"SITE_CAUSALES");
            $pm->throwForbidden(!$isGranted);
        }
        
        // creamos cliente y nos logeamos
        $crawlerhelper = $this->get("crawler.helper");
        $client = $crawlerhelper->getSgadClient();
        $logged = $crawlerhelper->getLoggedCrawler($client);
        $crawler = $logged[0];
        if($crawler==null)
        {
            throw $this->createNotFoundException();
        }
        $em = $this->getDoctrine()->getManager();
        $alumno = $em->getRepository('pDevAlumnosBundle:Alumno')->findOneByRut($rut);
        if($alumno)
        {
            // accedemos al motor de fotos
            
            $crawler = $client->request('GET', 'https://www4.uc.cl/SGAD/aplicacion/foto.jsp?rut='.$ch->parseRut($alumno->getRut(),false));
            
            $data = $client->getResponse()->getContent();
            
            return new Response(
                $data,
                200,
                array('content-type' => 'image/gif')
            );
        }
        else
            throw $this->createNotFoundException();
        
    }
    
    /**
     * @Route("/alumnos/{rut}/carga/{nalumno}/{curriculum}", name="obtener_carga")
     */
    public function getCargaAcademicaAction($nalumno,$curriculum,$rut)
    {
        $ch = $this->get("context.helper");
        $rut = $ch->parseRut($rut);
        $nalumno = $ch->parseNumeroAlumno($nalumno);
        $curriculum = $ch->parseCurriculum($curriculum);
        
        $pm = $this->get("permission.manager");
        if(!($pm->checkType("TYPE_ALUMNO") and $pm->getUser()->getPersona('TYPE_ALUMNO')->getRut()==$rut))
        {
            if(!$pm->checkType("TYPE_ACADEMICO"))
            {
               $isGranted = $pm->isGranted('ROLE_SUPER_USER',"SITE_ALUMNOS") || $pm->isGranted('ROLE_SUPER_USER',"SITE_CAUSALES");
               $pm->throwForbidden(!$isGranted);
            }
        }
        
        $em = $this->getDoctrine()->getManager();
        $alumno = $em->getRepository('pDevAlumnosBundle:Alumno')->findOneByRut($rut);
        if(!$alumno)
            throw $this->createNotFoundException('Unable to find Alumno entity.');
        
        $numeroalumno = $em->getRepository('pDevAlumnosBundle:AlumnoNumero')->findOneBy(array('alumno'=>$alumno->getId(),'numeroAlumno'=>$nalumno));
        if(!$numeroalumno)
            throw $this->createNotFoundException('Unable to find AlumnoNumero entity.');
            
        $curr = $em->getRepository('pDevAlumnosBundle:Curriculum')->findOneByCodigo($curriculum);
        if(!$curr)
            throw $this->createNotFoundException('Unable to find Curriculum entity.');
            
        $academicos = $em->getRepository('pDevAlumnosBundle:Academicos')->findOneBy(array('alumno'=>$numeroalumno->getId(),'curriculum'=>$curr->getId()));
        if (!$academicos)
            throw $this->createNotFoundException('Unable to find Academicos entity.');
        
        if($academicos)
        {
            // creamos cliente y nos logeamos
            $crawlerhelper = $this->get("crawler.helper");
            $client = $crawlerhelper->getSgadClient();
            $logged = $crawlerhelper->getLoggedCrawler($client);
            $crawler = $logged[0];
            if($crawler==null)
            {
                throw $this->createNotFoundException();
            }
            // accedemos al motor de fotos
            $crawler = $client->request('GET', 'https://www4.uc.cl/SGAD/aplicacion/exportar_pdf_BM.jsp?tipo_pagina=CA&cod_alumno='.$academicos->getCodigoInterno());
            
            $data = $client->getResponse()->getContent();
            //$headers = $client->getResponse()->getHeaders();
            //print_r($headers);
            //exit;
            $response = new Response(
                $data,
                200,
                array('content-type' => 'application/download','content-disposition'=>'attachment; filename="CargaAcademica_'.$academicos->getAlumno()->getNumeroAlumno().'.pdf"')
            );
            return $response;
        }
        
        throw $this->createNotFoundException();
    }
    
    /**
     * @Route("/alumnos/{rut}/ficha/{nalumno}/{curriculum}", name="obtener_ficha")
     */
    public function getFichaAcademicaAction($rut,$nalumno = null,$curriculum = null)
    {
        $ch = $this->get("context.helper");
        $rut = $ch->parseRut($rut);
        $nalumno = $ch->parseNumeroAlumno($nalumno);
        $curriculum = $ch->parseCurriculum($curriculum);
        
        $pm = $this->get("permission.manager");
        if(!($pm->checkType("TYPE_ALUMNO") and $pm->getUser()->getPersona('TYPE_ALUMNO')->getRut()==$rut))
        {
            if(!$pm->checkType("TYPE_ACADEMICO"))
            {
                $isGranted = $pm->isGranted('ROLE_SUPER_USER',"SITE_ALUMNOS") || $pm->isGranted('ROLE_SUPER_USER',"SITE_CAUSALES");
                $pm->throwForbidden(!$isGranted);
            }
        }
        
        $em = $this->getDoctrine()->getManager();
        $alumno = $em->getRepository('pDevAlumnosBundle:Alumno')->findOneByRut($rut);
        if(!$alumno)
            throw $this->createNotFoundException('Unable to find Alumno entity.');
        
        $academicos = null;
        if($nalumno and $curriculum)
        {
            $numeroalumno = $em->getRepository('pDevAlumnosBundle:AlumnoNumero')->findOneBy(array('alumno'=>$alumno->getId(),'numeroAlumno'=>$nalumno));
            if(!$numeroalumno)
                throw $this->createNotFoundException('Unable to find AlumnoNumero entity.');

            $curr = $em->getRepository('pDevAlumnosBundle:Curriculum')->findOneByCodigo($curriculum);
            if(!$curr)
                throw $this->createNotFoundException('Unable to find Curriculum entity.');

            $academicos = $em->getRepository('pDevAlumnosBundle:Academicos')->findOneBy(array('alumno'=>$numeroalumno->getId(),'curriculum'=>$curr->getId()));
            if (!$academicos)
                throw $this->createNotFoundException('Unable to find Academicos entity.');

        }
        else
        {
            $numeroalumno_t = $em->getRepository('pDevAlumnosBundle:AlumnoNumero')->findBy(array('alumno'=>$alumno->getId()));
            if(!$numeroalumno_t)
                throw $this->createNotFoundException('Unable to find AlumnoNumero entity.');
            
            $naquery = '';
            
            $counter = count($numeroalumno_t);
            foreach($numeroalumno_t as $na)
            {
                $counter--;
                $naquery .= 'na.id = '.$na->getId(); 
                if($counter>0)
                    $naquery .= ' or ';
            }
            
            
            $situacion = $em->getRepository('pDevAlumnosBundle:Situacion')->findOneByNombre('Regular');
            if (!$situacion)
                throw $this->createNotFoundException('Unable to find situacion:regular entity.');
            
            $qb = $em->getRepository('pDevAlumnosBundle:Academicos')->createQueryBuilder('i');
            $results = $qb->leftJoin('i.alumno','na')
                    ->leftJoin('i.situacionAcademica','sit')
                    ->where($naquery)
                    ->andWhere('sit.id = '.$situacion->getId())
                    ->orderBy('i.ingresoYear','DESC')
                    ->getQuery()
                    ->getResult();
            
            if(count($results)==0)
            {
                $results = $qb->where($naquery)                    
                    ->orderBy('i.ingresoYear','DESC')
                    ->getQuery()
                    ->getResult();
            }
            
            if(count($results)>0)
                $academicos = $results[0];
            
            if (!$academicos)
                throw $this->createNotFoundException('Unable to find Academicos entity.');
        }
        
        if($academicos)
        {
            // creamos cliente y nos logeamos
            $crawlerhelper = $this->get("crawler.helper");
            $client = $crawlerhelper->getSgadClient();
            $logged = $crawlerhelper->getLoggedCrawler($client);
            $crawler = $logged[0];
            if($crawler==null)
            {
                throw $this->createNotFoundException();
            }
            // accedemos al motor de fotos
            $crawler = $client->request('GET', 'https://www4.uc.cl/SGAD/aplicacion/exportar_pdf_BM.jsp?tipo_pagina=FA&cod_alumno='.$academicos->getCodigoInterno());
            
            $data = $client->getResponse()->getContent();
            //$headers = $client->getResponse()->getHeaders();
            //print_r($headers);
            //exit;
            $response = new Response(
                $data,
                200,
                array('content-type' => 'application/download','content-disposition'=>'attachment; filename="FichaAcademica_'.$academicos->getAlumno()->getNumeroAlumno().'.pdf"')
            );

            return $response;
        }
        
        throw $this->createNotFoundException();
    }
}
