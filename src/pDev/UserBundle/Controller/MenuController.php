<?php

namespace pDev\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class MenuController extends Controller
{
    /**
     * @Template()
     */
    public function menuAction()
    {
        $items = array();
        $pm = $this->get("permission.manager");
        $user = $pm->getUser();
        
        if($user and $user->getRut())
        {
            $ch = $this->get('context.helper'); 
                    
            $isExterno = $user->getExternal();
            $isAlumno = $pm->checkType("TYPE_ALUMNO");
            $isSupervisor = $pm->checkType("TYPE_PRACTICAS_SUPERVISOR");
            $isContacto = $pm->checkType("TYPE_PRACTICAS_CONTACTO");
            $isCoordinacion = $pm->isGranted("ROLE_ADMIN","SITE_PRACTICAS");
            $isAcademico = $pm->checkType("TYPE_ACADEMICO");
            $isFuncionario = $pm->checkType("TYPE_FUNCIONARIO");
                        
            $items[] = $this->createItem('Inicio',$this->generateUrl('default_inicio'));
            //$items[] = $this->createItem('Notificaciones',$this->generateUrl('notificaciones'));
            
            $practicas_items = array();
            //$practicas_items[] = $this->createItem('Foro',$this->generateUrl('practicas',array('filtro'=>'foro')));
            
            if(!$isAcademico)
                $practicas_items[] = $this->createItem('Ofertas',$this->generateUrl('practicas'));
            
            if($isCoordinacion or $isAlumno)
                $practicas_items[] = $this->createItem('Planes de práctica',$this->generateUrl('practicas_alumno'));
            elseif($isSupervisor or $isContacto){
                $practicas_items[] = $this->createItem('Practicantes',$this->generateUrl('practicas_alumno'));
                $practicas_items[] = $this->createItem('Organizaciones',$this->generateUrl('practicas_organizacion'));
            }
            
            if($isCoordinacion or $isAcademico)
                $practicas_items[] = $this->createItem('Evaluaciones',$this->generateUrl('practicas_evaluacion'));
            
            $items = array_merge($items,$practicas_items);     
            
            //solo si soy super user o admin
            // personas
            
            $admin_items = array();
            
            if($isCoordinacion)
            {
                $admin_items[] = $this->createItem('Organizaciones',$this->generateUrl('practicas_organizacion'));
                $admin_items[] = $this->createItem('Supervisores',$this->generateUrl('practicas_supervisor'));
            }
            
            if($pm->isGranted("ROLE_SUPER_USER","SITE_PERSONAS"))
            {
                //$admin_items[] = $this->createItem('Todas',$this->generateUrl('persona'));
                $admin_items[] = $this->createItem('Contactos',$this->generateUrl('persona_contactos'));
                $admin_items[] = $this->createItem('Académicos',$this->generateUrl('persona_profesores'));
                $admin_items[] = $this->createItem('Funcionarios',$this->generateUrl('persona_funcionarios'));
            }
            
            if($pm->isGranted("ROLE_SUPER_USER","SITE_ALUMNOS"))
            {
                $admin_items[] = $this->createItem('Alumnos',$this->generateUrl('persona_alumnos'));
            }
            
            if($pm->isGranted("ROLE_SUPER_USER","SITE_PERMISOS"))
                $admin_items[] = $this->createItem('Permisos',$this->generateUrl('user'));
            
            $ajustes_granted = $pm->isGranted("ROLE_SUPER_USER","SITE_AJUSTES");            
            if($ajustes_granted){
                $admin_items[] = $this->createItem('Preguntas frecuentes',$this->generateUrl('preguntafrecuente'));
                $admin_items[] = $this->createItem('Documentos',$this->generateUrl('documento'));
                $admin_items[] = $this->createItem('Ajustes generales',$this->generateUrl('configuracion'));
            }
            // merge menu
            
            if(count($admin_items)>0)
            {
                $admin = $this->createItem('Administrar');
                $admin['children'] = $admin_items;
                $items[] = $admin;
            }
        }
        
        return array('items' => $items);
    }
    
    private function createItem($text,$link=null)
    {
        $isHeader = false;
        if(!$link)
            $isHeader = true;

        return array('isHeader'=>$isHeader,
                        'isActive'=>$this->matchLink($link),
                        'link'=>$link,
                        'text'=>$text,
                        'children'=>array()
                    );
    }
    
    private function matchLink($link)
    {
        $uri = $this->container->get('request')->server->get('PHP_SELF');
        $devuri = "/app_dev.php";
        //print_r($this->container->get('request')->server);
        //exit;
        // si la url actual tiene app_dev se la sacamos
        if(substr($uri, 0, strlen($devuri)) === $devuri)
            $uri = substr($uri, strlen($devuri));
        else
            $uri = $this->container->get('request')->server->get('REDIRECT_URL');
        
        // si el link actual tiene app_dev se la sacamos
        if(substr($link, 0, strlen($devuri)) === $devuri)
            $link = substr($link, strlen($devuri));
            
        // si la url actual tiene la barra final se la sacamos    
        if(strlen($uri) > 2 and substr($uri, strlen($uri)-1) === '/')
            $uri = substr($uri, 0, strlen($uri)-1);

        // si el link actual tiene la barra final se la sacamos    
        if(strlen($link) > 2 and substr($link, strlen($link)-1) === '/')
            $link = substr($link, 0, strlen($link)-1);
        
        //print_r($link);
        
        
        
        if(!$link)
        {
            return false;
        }
    	elseif ($link === $uri) 
    	{
    		// URL's completely match
    		return true;
        } 
        elseif($link !== '/' && (substr($uri, 0, strlen($link)) === $link)) 
        {
        	// URL isn't just "/" and the first part of the URL match
        	return true;
    	}
        return false;
    }
}
