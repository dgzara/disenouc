<?php

namespace pDev\ParserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use pDev\UserBundle\Entity\Profesor;
use pDev\UserBundle\Entity\ProfesorAlias;
use pDev\UserBundle\Entity\Funcionario;
use pDev\UserBundle\Entity\Notificacion;
use pDev\UserBundle\Entity\Alumno;
use pDev\UserBundle\Entity\Periodo;

use pDev\CausalesBundle\Entity\Articulo;
use pDev\CausalesBundle\Entity\Causal;
use pDev\CausalesBundle\Entity\ArticuloCurso;
use pDev\CausalesBundle\Entity\ArticuloTipo;
use pDev\CausalesBundle\Entity\ArticuloCursoNota;

/**
 * Importa controller.
 *
 * @Route("/importa")
 */
class ImportaController extends Controller
{
    /**
     * @Route("/personas", name="importa_personas")
     * @Template()
     */
    public function personasAction()
    {
        // chequeo permiso
        $this->get("permission.manager")->isGrantedForbidden("ROLE_ADMIN","SITE_PERSONAS");
        
        return array();
    }
    
    /**
     * @Route("/personas/plantilla", name="importa_personas_plantilla")
     */
    public function personasPlantillaAction()
    {
        // chequeo permiso
        $this->get("permission.manager")->isGrantedForbidden("ROLE_ADMIN","SITE_PERSONAS");
        
        return array();
    }
    
    /**
     * @Route("/causales", name="importa_causales")
     * @Template()
     */
    public function causalesAction()
    {
        // chequeo permiso
        $this->get("permission.manager")->isGrantedForbidden("ROLE_ADMIN","SITE_CAUSALES");
        return array();
    }
    
    /**
     * @Route("/personas/profesores/upload", name="importa_profesores_upload")
     * @Template()
     */
    public function profesoresUploadAction()
    {
        
        // chequeo permiso
        $this->get("permission.manager")->isGrantedForbidden("ROLE_ADMIN","SITE_PERSONAS");
        
        $form = $this->createFormBuilder()
            ->add('archivo', 'file', array('label' => 'Seleccionar Archivo (xls xlsx) ','attr' => array(
                        'style' => 'opacity:100 !important;', )))
            ->getForm();
        $request = $this->getRequest();
        
        if ($request->isMethod('POST'))
        {
            $form->bind($request);

            if ($form->isValid())
            {
                $file = $form['archivo']->getData();
                $nm = $this->get("notification.manager");
                if($file)
                {
                    $ch = $this->get("context.helper");
                    $extension = $ch->lowerizeText(pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION));
                    $mimetype = $file->getClientMimeType();
                    if (!$extension)
                        $extension = 'No se pudo determinar la extensión';
                    if (!$mimetype)
                        $mimetype = 'No se pudo determinar el mime type';
                    
                    $session = $this->getRequest()->getSession();
                    
                    if($file && isset($extension) && isset($mimetype) && ($extension == 'xls' || $extension == 'xlsx'))
                    {
                        $profesores = $this->parseArchivo($file);
                        
                        $counter=0;
                        foreach($profesores as $profesor)
                        {
                            $result = $this->createProfesor($profesor);
                            if($result=="ok")
                                $counter++;
                            else
                            {
                                $nm->createNotificacion('Error', 'Ocurrio un error, intentelo mas tarde,: '.implode(',',$profesor),
                                            Notificacion::USER_ERROR
                                            );                                
                            }                
                        }
                        
                        $nm->createNotificacion('Importación realizada', 'La importación se realizó con éxito.',
                                            Notificacion::USER_SUCCESS
                                            );
                    }
                    else
                    {
                        $nm->createNotificacion('Error en la importación', 'No se pudo determinar la extensión o el tipo de archivo.',
                                            Notificacion::USER_ERROR
                                            );
                    }
                }
                else
                {
                    $nm->createNotificacion('Error en la importación', 'Debe seleccionar un archivo',
                                            Notificacion::USER_ERROR
                                            );
                }

            }
            
            return $this->redirect($this->generateUrl('importa_personas'));
        }
        
        return array(
            'form' => $form->createView(),
        );  
    }
    
    /**
     * @Route("/personas/funcionarios/upload", name="importa_funcionarios_upload")
     * @Template()
     */
    public function funcionariosUploadAction()
    {
        
        // chequeo permiso
        $this->get("permission.manager")->isGrantedForbidden("ROLE_ADMIN","SITE_PERSONAS");
        
        $form = $this->createFormBuilder()
            ->add('archivo', 'file', array('label' => 'Seleccionar Archivo (xls xlsx) ','attr' => array(
                        'style' => 'opacity:100 !important;', )))
            ->getForm();
        $request = $this->getRequest();
        
        if ($request->isMethod('POST'))
        {
            $form->bind($request);

            if ($form->isValid())
            {
                $file = $form['archivo']->getData();
                $nm = $this->get("notification.manager");
                if($file)
                {
                    $ch = $this->get("context.helper");
                    $extension = $ch->lowerizeText(pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION));
                    $mimetype = $file->getClientMimeType();
                    if (!$extension)
                        $extension = 'No se pudo determinar la extensión';
                    if (!$mimetype)
                        $mimetype = 'No se pudo determinar el mime type';
                    
                    $session = $this->getRequest()->getSession();
                    
                    if($file && isset($extension) && isset($mimetype) && ($extension == 'xls' || $extension == 'xlsx'))
                    {
                        $funcionarios = $this->parseArchivo($file);
                        
                        $counter=0;
                        foreach($funcionarios as $funcionario)
                        {
                            $result = $this->createFuncionario($funcionario);
                            if($result=="ok")
                                $counter++;
                            else
                            {
                                $nm->createNotificacion('Error', 'Ocurrio un error, intentelo mas tarde,: '.implode(',',$funcionario),
                                            Notificacion::USER_ERROR
                                            );
                            }                
                        }
                        
                        $nm->createNotificacion('Importación completada', 'La importación se realizó con éxito.',
                                            Notificacion::USER_SUCCESS
                                            );
                    }
                    else
                    {
                        $nm->createNotificacion('Error en la importación', 'No se pudo determinar la extensión o el tipo de archivo.',
                                            Notificacion::USER_ERROR
                                            );
                    }
                }
                else
                {
                    $nm->createNotificacion('Error en la importación', 'Debe seleccionar un archivo',
                                            Notificacion::USER_ERROR
                                            );
                }

            }
            
            return $this->redirect($this->generateUrl('importa_personas'));
        }
        
        return array(
            'form' => $form->createView(),
        );  
    }
    
    /**
     * @Route("/personas/alumnos/upload", name="importa_alumnos_upload")
     * @Template()
     */
    public function alumnosUploadAction()
    {
        
        // chequeo permiso
        $this->get("permission.manager")->isGrantedForbidden("ROLE_ADMIN","SITE_PERSONAS");
        
        $form = $this->createFormBuilder()
            ->add('archivo', 'file', array('label' => 'Seleccionar Archivo (xls xlsx) ','attr' => array(
                        'style' => 'opacity:100 !important;', )))
            ->getForm();
        $request = $this->getRequest();
        
        if ($request->isMethod('POST'))
        {
            $form->bind($request);

            if ($form->isValid())
            {
                $file = $form['archivo']->getData();
                $nm = $this->get("notification.manager");
                if($file)
                {
                    $ch = $this->get("context.helper");
                    $extension = $ch->lowerizeText(pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION));
                    $mimetype = $file->getClientMimeType();
                    if (!$extension)
                        $extension = 'No se pudo determinar la extensión';
                    if (!$mimetype)
                        $mimetype = 'No se pudo determinar el mime type';
                    
                    $session = $this->getRequest()->getSession();
                    
                    if($file && isset($extension) && isset($mimetype) && ($extension == 'xls' || $extension == 'xlsx'))
                    {
                        $alumnos = $this->parseArchivo($file);
                        
                        $counter=0;
                        foreach($alumnos as $alumno)
                        {
                            $result = $this->createAlumno($alumno);
                            if($result=="ok")
                                $counter++;
                            else
                            {
                                $nm->createNotificacion('Error', 'Ocurrio un error, intentelo mas tarde,: '.implode(',',$alumno),
                                            Notificacion::USER_ERROR
                                            );                                
                            }                
                        }
                        
                        $nm->createNotificacion('Importación completada', 'La importación se realizó con éxito.',
                                            Notificacion::USER_SUCCESS
                                            );
                    }
                    else
                    {
                        $nm->createNotificacion('Error en la importación', 'No se pudo determinar la extensión o el tipo de archivo.',
                                            Notificacion::USER_ERROR
                                            );
                    }
                }
                else
                {
                    $nm->createNotificacion('Error en la importación', 'Debe seleccionar un archivo',
                                            Notificacion::USER_ERROR
                                            );
                }

            }
            
            return $this->redirect($this->generateUrl('importa_personas'));
        }
        
        return array(
            'form' => $form->createView(),
        );  
    }
    
    /**
     * @Route("/causales/upload", name="importa_causales_upload")
     * @Template()
     */
    public function causalesUploadAction()
    {
        
        // chequeo permiso
        $this->get("permission.manager")->isGrantedForbidden("ROLE_ADMIN","SITE_CAUSALES");
        
        $form = $this->createFormBuilder()
            ->add('archivo', 'file', array('label' => 'Seleccionar Archivo (.xls .xlsx) ','attr' => array(
                        'style' => 'opacity:100 !important;', )))
            ->getForm();
        $request = $this->getRequest();
        
        if ($request->isMethod('POST'))
        {
            $form->bind($request);

            if ($form->isValid())
            {
                $file = $form['archivo']->getData();
                $nm = $this->get("notification.manager");
                if($file)
                {
                    $ch = $this->get("context.helper");
                    $extension = $ch->lowerizeText(pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION));
                    $mimetype = $file->getClientMimeType();
                                        
                                        
                    if($file && isset($extension) && isset($mimetype) && ($extension == 'xls' || $extension == 'xlsx'))
                    {
                        
                        $error = null;
                        $filas;
                        try
                        {
                            $filas = $this->parseArchivo($file);
                        }
                        catch(\Exception $e)
                        {
                            $error = 'al leer el archivo';
                        }
                        $causales;
                        try
                        {
                            $causales = $this->parseCausales($filas);
                            //print_r($causales);
                            //exit;
                        }
                        catch(\Exception $e)
                        {
                            $error = 'al interpretar datos';
                            
                        }
                        
                        if($error)
                        {
                            $nm->createNotificacion('Error', 'Ocurrió un error: '.$error,
                                        Notificacion::USER_ERROR
                                        );
                        } 
                        else
                        {
                            $counter=0;
                            foreach($causales as $causal)
                            {
                                $result = $this->createCausal($causal);
                                if($result=="ok")
                                    $counter++;
                                else
                                {
                                    $nm->createNotificacion('Error', 'Ocurrió un error, al crear causal: '.$result,
                                                Notificacion::USER_ERROR
                                                );
                                }                
                            }


                            $nm->createNotificacion('Importación completada', 'La importación se realizó con éxito.',
                                                Notificacion::USER_SUCCESS
                                                );
                        }
                    }
                    else
                    {
                        $nm->createNotificacion('Error en la importación', 'Tipo de archivo no permitido.',
                                            Notificacion::USER_ERROR
                                            );
                    }
                }
                else
                {
                    $nm->createNotificacion('Error en la importación', 'Debe seleccionar un archivo',
                                            Notificacion::USER_ERROR
                                            );
                }

            }
            
            return $this->redirect($this->generateUrl('importa_causales'));
        }
        
        return array(
            'form' => $form->createView(),
        );  
    }
    
    private function parseCausales($filas)
    {
        $ch = $this->get('context.helper');
        $causales = array();
        
        foreach($filas as $fila)
        {
            $arraykey = $fila['nroalumno'].':'.$fila['codcurriculum'];
            $causal;
            if(array_key_exists($arraykey, $causales))
            {
                $causal = $causales[$arraykey];
            }
            else
            {
                $causal = array();
                $causal['numeroalumno'] = $fila['nroalumno'];
                $causal['curriculum'] = $fila['codcurriculum'];
                $causal['nombre'] = $fila['nom'];
                $causal['articulos'] = array();
            }
            
            
            $articulo;
            $art = $fila['art'];
            $string = $ch->trimText($fila['string']);
            
            if(strpos($string, 'ARTICULO 30')===FALSE)
            {
                if(array_key_exists($art, $causal['articulos']))
                {
                    $articulo = $causal['articulos'][$art];
                }
                else
                {
                    $articulo = array();
                    $articulo['nombre'] = $art;                    
                    $articulo['cursos'] = array();
                }

                if($art==='30a')
                {
                    if(strpos($string, 'PPA')===FALSE)
                    {
                        $count = explode(' ', $string);
                        if(count($count)==3)
                        {                            
                            list($ppa,$sem,$year) = $count;
                            $articulo['ppa'] = $ppa;
                            $articulo['sem'] = $sem;
                            $articulo['year'] = $year;
                        }
                    }
                }
                elseif($art==='30b')
                {
                    $values = explode(' ',$string);
                    $values2 = array();
                    foreach($values as $v)
                    {
                        if($v!==':')
                            $values2 = array_merge($values2,explode(':',$v));
                    }
                    
                    for($i = 0;$i<count($values2)-1;$i++)
                    {
                        if($values2[$i]==='CR.EXIGIDO')
                        {
                            $articulo['cr.exigido'] = $values2[$i+1];
                            break;
                        }
                    }
                }
                elseif($art==='30c')
                {
                    if(strpos($string, 'SIGLA ')===FALSE)
                    {
                        $values = explode(' ',$string);
                        $sigla = $values[0];
                        $curso;                        
                        if(array_key_exists($sigla, $articulo['cursos']))
                        {
                            $curso = $articulo['cursos'][$sigla];
                        }
                        else
                        {
                            $curso = array();
                            $curso['sigla'] = $sigla;
                            $curso['cred'] = $values[1];
                            $curso['notas'] = array();
                        }
                        
                        $nota1 = array();
                        $nota1['sem'] = $values[2];
                        $nota1['ano'] = $values[3];
                        $nota1['nota'] = $values[4];
                        $nota1['tipo'] = $values[5];
                        
                        $curso['notas'][] = $nota1;
                        
                        if(count($values)>8)
                        {
                            $nota2 = array();
                            $nota2['sem'] = $values[6];
                            $nota2['ano'] = $values[7];
                            $nota2['nota'] = $values[8];
                            $nota2['tipo'] = $values[9];
                            
                            $curso['notas'][] = $nota2;
                        }
                        if(count($values)>12)
                        {
                            $nota3 = array();
                            $nota3['sem'] = $values[10];
                            $nota3['ano'] = $values[11];
                            $nota3['nota'] = $values[12];
                            $nota3['tipo'] = $values[13];
                            
                            $curso['notas'][] = $nota3;
                        }
                        
                        $articulo['cursos'][$sigla] = $curso;
                                                
                    }
                }
                elseif($art==='30d')
                {
                    if(strpos($string, 'SIGLA ')===FALSE)
                    {
                        $values = explode(' ',$string);
                        $sigla = $values[0];
                        $curso;                        
                        if(array_key_exists($sigla, $articulo['cursos']))
                        {
                            $curso = $articulo['cursos'][$sigla];
                        }
                        else
                        {
                            $curso = array();
                            $curso['sigla'] = $sigla;
                            $curso['cred'] = $values[1];
                            $curso['notas'] = array();
                        }
                        
                        $nota1 = array();
                        $nota1['sem'] = $values[2];
                        $nota1['ano'] = $values[3];
                        $nota1['nota'] = $values[4];
                        $nota1['tipo'] = $values[5];
                        
                        $curso['notas'][] = $nota1;
                        
                        if(count($values)>8)
                        {
                            $nota2 = array();
                            $nota2['sem'] = $values[6];
                            $nota2['ano'] = $values[7];
                            $nota2['nota'] = $values[8];
                            $nota2['tipo'] = $values[9];
                            
                            $curso['notas'][] = $nota2;
                        }
                        if(count($values)>12)
                        {
                            $nota3 = array();
                            $nota3['sem'] = $values[10];
                            $nota3['ano'] = $values[11];
                            $nota3['nota'] = $values[12];
                            $nota3['tipo'] = $values[13];
                            
                            $curso['notas'][] = $nota3;
                        }
                        
                        $articulo['cursos'][$sigla] = $curso;
                                                
                    }
                }
                elseif($art==='30e')
                {
                    if(strpos($string, 'SIGLA ')===FALSE and strpos($string, 'CURSADOS')===FALSE)
                    {
                        $values = explode(' ',$string);
                        $curso;
                        $l = count($values);
                        
                        $sigla = $values[0];
                        if(is_numeric($sigla))
                            continue;
                        if(array_key_exists($sigla, $articulo['cursos']))
                        {
                            $curso = $articulo['cursos'][$sigla];
                        }
                        else
                        {
                            $curso = array();
                            $curso['sigla'] = $sigla;
                            $curso['cred'] = $values[1];                            
                            $curso['notas'] = array();
                        }
                        
                        $nota1 = array();
                        $nota1['sem'] = $values[$l-3];
                        $nota1['ano'] = $values[$l-2];
                        $nota1['nota'] = $values[$l-1];                        
                        
                        $curso['notas'][] = $nota1;
                        
                        $articulo['cursos'][$sigla] = $curso;
                                                
                    }
                }

                $causal['articulos'][$art] = $articulo;
            }
            
            $causales[$arraykey] = $causal;
        }
        
        
            
        
        return $causales;
    }
    
    private function parseArchivo($file)
    {
        $es = $this->get('xls.load_xls2007');        
        //caching mode        
        $eo = $es->load($file);
        
        $ws = $eo->setActiveSheetIndex(0);
        $ch = $this->get("context.helper");
        
        $filas = array();
        
        $max_cols = 50;
        $max_rows = 500;
        
        $isCaption = true;
        
        $invalida_fila=0;
        $rowIterator = $ws->getRowIterator();
        foreach ($rowIterator as $row)
        {
            $cellIterator = $row->getCellIterator();            
            $cellIterator->setIterateOnlyExistingCells(false);
            
            $invalid=0;
            $fila_array = array();
            foreach ($cellIterator as $cell) 
            {
                if ($cell)
                {
                    $valor = null;
                    if($isCaption)
                        $valor = $ch->clearText($cell->getCalculatedValue());
                    else
                        $valor = $ch->normalizeText($cell->getCalculatedValue());
                    
                    $fila_array[] = $valor;
                    $cols_count = count($fila_array);
                    
                    if(!empty($valor) and $valor != "0")
                    {
                        $invalid = 0;
                    }
                    else
                    {
                        $invalid++;
                        if($invalid==15)
                        {
                            $fila_array = array_slice($fila_array, 0, $cols_count - $invalid);
                            break;
                        }
                        
                    }
                    
                }
                
                if($cellIterator->key()>$max_cols)
                    break;
            }
            $isCaption = false;
            
            $filas[] = $fila_array;
            $rows_count = count($filas);
            
            if(!empty($fila_array))
            {
                $invalida_fila = 0;
            }
            else
            {
                $invalida_fila++;
                if($invalida_fila==15)
                {
                    $filas = array_slice($filas, 0, $rows_count - $invalida_fila);
                    break;
                }
                
            }
            
            if($rows_count>$max_rows)
                break;          
        }
        
        if(count($filas)<2)
        {
            return array();
        }
        
        $captions = $filas[0];
        $cols_count = count($captions);
        $rows = array();
        $filas = array_slice($filas, 1);
        
        foreach($filas as $row_array)
        {
            $row = array();
            for($j=0;$j<$cols_count;$j++)
            {
                    $valor = null;
                    if(array_key_exists($j, $row_array))
                        $valor = $row_array[$j];
                    if(!empty($captions[$j]))
                        $row[$captions[$j]] = $valor;
                    else
                        $row[] = $valor;
            }
                    
            $rows[] = $row;
        }
        
        return $rows;
        
    }
    
    private function createProfesor($profesor_array)
    {
        $em = $this->getDoctrine()->getManager();
        
        //chequeamos columnas
        $llaves = array();
        $llaves[] = "rut";
        $llaves[] = "nombres";
        $llaves[] = "apellidopaterno";
        $llaves[] = "apellidomaterno";
        $llaves[] = "emailalternativo";
        $llaves[] = "emailuc";
        
        $llaves_ausentes = array();
        foreach($llaves as $llave)
        {
            if(!array_key_exists($llave,$profesor_array))
                $llaves_ausentes[] = $llave;
        }
        
        if(count($llaves_ausentes)>0)
            return 'no encontrada(s): '. implode(',',$llaves_ausentes);
        
        $ch = $this->get("context.helper");
        
        // normalizamos datos
        $profesor_rut = $ch->parseRut($profesor_array['rut']);
        $profesor_nombres = $ch->parseNombre($profesor_array['nombres']);
        $profesor_apellidopaterno = $ch->parseNombre($profesor_array['apellidopaterno']);
        $profesor_apellidomaterno = $ch->parseNombre($profesor_array['apellidomaterno']);
        $profesor_emailalternativo = $ch->parseEmail($profesor_array['emailalternativo']);
        $profesor_emailuc = $ch->parseEmail($profesor_array['emailuc'],array('uc.cl','puc.cl'));
        
        if(empty($profesor_rut))
            return 'rut no debe estar vacío';
        if(empty($profesor_nombres))
            return 'nombres no debe estar vacío';
        if(empty($profesor_apellidopaterno))
            return 'apellido paterno no debe estar vacío';
                
        // creamos profesor
        $profesor = $em->getRepository('pDevCursosBundle:Profesor')->findOneByRut($profesor_rut);
        if(!$profesor)
        {
            $profesor = new Profesor();
            $profesor->setRut($profesor_rut);
            $em->persist($profesor);
        }
        $profesor->setNombres($profesor_nombres);
        $profesor->setApellidoPaterno($profesor_apellidopaterno);
        $profesor->setApellidoMaterno($profesor_apellidomaterno);
        $profesor->setEmailSecundario($profesor_emailalternativo);
        $profesor->setEmail($profesor_emailuc);
        
        $alias = $ch->generateAlias($profesor_nombres.' '.$profesor_apellidopaterno.' '.$profesor_apellidomaterno);
        
        $profesoralias = $em->getRepository('pDevCursosBundle:ProfesorAlias')->findOneByAlias($alias);
        if(!$profesoralias and !empty($alias))
        {
            $profesoralias = new ProfesorAlias();
            $profesoralias->setAlias($alias);
            $em->persist($profesoralias);
        }
        $profesoralias->setProfesor($profesor);
            
        $em->flush();
        return "ok";
    }
    
    private function createFuncionario($funcionario_array)
    {
        $em = $this->getDoctrine()->getManager();
        
        //chequeamos columnas
        $llaves = array();
        $llaves[] = "rut";
        $llaves[] = "nombres";
        $llaves[] = "apellidopaterno";
        $llaves[] = "apellidomaterno";
        $llaves[] = "emailalternativo";
        $llaves[] = "emailuc";
        
        $llaves_ausentes = array();
        foreach($llaves as $llave)
        {
            if(!array_key_exists($llave,$funcionario_array))
                $llaves_ausentes[] = $llave;
        }
        
        if(count($llaves_ausentes)>0)
            return 'no encontrada(s): '. implode(',',$llaves_ausentes);
        
        $ch = $this->get("context.helper");
        
        // normalizamos datos
        $funcionario_rut = $ch->parseRut($funcionario_array['rut']);
        $funcionario_nombres = $ch->parseNombre($funcionario_array['nombres']);
        $funcionario_apellidopaterno = $ch->parseNombre($funcionario_array['apellidopaterno']);
        $funcionario_apellidomaterno = $ch->parseNombre($funcionario_array['apellidomaterno']);
        $funcionario_emailalternativo = $ch->parseEmail($funcionario_array['emailalternativo']);
        $funcionario_emailuc = $ch->parseEmail($funcionario_array['emailuc'],array('uc.cl','puc.cl'));
        
        if(empty($funcionario_rut))
            return 'rut no debe estar vacío';
        if(empty($funcionario_nombres))
            return 'nombres no debe estar vacío';
        if(empty($funcionario_apellidopaterno))
            return 'apellido paterno no debe estar vacío';
        
        // creamos funcionario
        $funcionario = $em->getRepository('pDevUserBundle:Funcionario')->findOneByRut($funcionario_rut);
        if(!$funcionario)
        {
            $funcionario = new Funcionario();
            $funcionario->setRut($funcionario_rut);
            $em->persist($funcionario);
        }
        $funcionario->setNombres($funcionario_nombres);
        $funcionario->setApellidoPaterno($funcionario_apellidopaterno);
        $funcionario->setApellidoMaterno($funcionario_apellidomaterno);
        $funcionario->setEmailSecundario($funcionario_emailalternativo);
        $funcionario->setEmail($funcionario_emailuc);
        
                    
        $em->flush();
        return "ok";
    }
    
    private function createAlumno($alumno_array)
    {
        $em = $this->getDoctrine()->getManager();
        
        //chequeamos columnas
        $llaves = array();
        $llaves[] = "numeroalumno";
        $llaves[] = "nombres";
        $llaves[] = "apellidopaterno";
        $llaves[] = "apellidomaterno";
        $llaves[] = "emailuc";
        $llaves[] = "periodo";
        
        $llaves_ausentes = array();
        foreach($llaves as $llave)
        {
            if(!array_key_exists($llave,$alumno_array))
                $llaves_ausentes[] = $llave;
        }
        
        if(count($llaves_ausentes)>0)
            return 'no encontrada(s): '. implode(',',$llaves_ausentes);
        
        $ch = $this->get("context.helper");
        
        // normalizamos datos
        $alumno_numero = $ch->parseNumeroAlumno($alumno_array['numeroalumno']);
        $alumno_nombres = $ch->parseNombre($alumno_array['nombres']);
        $alumno_apellidopaterno = $ch->parseNombre($alumno_array['apellidopaterno']);
        $alumno_apellidomaterno = $ch->parseNombre($alumno_array['apellidomaterno']);
        $alumno_emailuc = $ch->parseEmail($alumno_array['emailuc'],array('uc.cl','puc.cl'));
        $alumno_periodo = $alumno_array['periodo'];
        
        if(empty($alumno_numero))
            return 'numero alumno no debe estar vacío';
        if(empty($alumno_nombres))
            return 'nombres no debe estar vacío';
        if(empty($alumno_apellidopaterno))
            return 'apellido paterno no debe estar vacío';
        
        
        
        if(empty($alumno_periodo))
        {
            $year = $ch->getYearActual();
            $semestre = $ch->getSemestreActual(); 
            $alumno_periodo = $year.'-'.$semestre;
        }
        
        $periodo2 = explode('-', $alumno_periodo);
        
        if(count($periodo2)==2)
        {
            $year = intval($periodo2[0]);
            $semestre = intval($periodo2[1]);
            $alumno_periodo = $year.'-'.$semestre;
        }
        
        // creamos el alumno
        $alumno = $em->getRepository('pDevUserBundle:Alumno')->findOneByNumeroAlumno($alumno_numero);
        if(!$alumno)
        {
            $alumno = new Alumno();
            $alumno->setNumeroAlumno($alumno_numero);
            $em->persist($alumno);
        }
        $alumno->setNombres($alumno_nombres);
        $alumno->setApellidoPaterno($alumno_apellidopaterno);
        $alumno->setApellidoMaterno($alumno_apellidomaterno);
        $alumno->setEmail($alumno_emailuc);        
            
        $periodo = $em->getRepository('pDevUserBundle:Periodo')->findOneBy(array('semestre'=>$semestre,'year'=>$year));
        if(!$periodo)
        {
            $periodo = new \pDev\UserBundle\Entity\Periodo();
            $periodo->setSemestre($semestre);
            $periodo->setYear($year);
            $em->persist($periodo);
        }
        if(!$periodo->getAlumnos()->contains($alumno))
        {
            $periodo->addAlumno($alumno);
            $alumno->addPeriodo($periodo);
        }
        $em->flush();
        
        return "ok";
    }
    
    private function createCausal($causal_array)
    {
        $em = $this->getDoctrine()->getManager();
        
        //chequeamos columnas
        $llaves = array();
        $llaves[] = "nombre";
        $llaves[] = "numeroalumno";
        $llaves[] = "curriculum";
        $llaves[] = "articulos";
        /*
        $llaves[] = "testdeactualidadaprobados";
        $llaves[] = "testdeactualidadreprobados";
        $llaves[] = "ppa";
        $llaves[] = "creditosinscritos";
        $llaves[] = "creditosaprobados";
        $llaves[] = "creditosconvalidados";
        $llaves[] = "crdnotap";
        $llaves[] = "creditosreprobados";
        $llaves[] = "suspensiondeestudios";
        $llaves[] = "anulaciondeestudios";*/
        
        
        $llaves_ausentes = array();
        foreach($llaves as $llave)
        {
            if(!array_key_exists($llave,$causal_array))
                $llaves_ausentes[] = $llave;
        }
        
        
                        
        if(count($llaves_ausentes)>0)
            return 'no encontrada(s): '. implode(',',$llaves_ausentes);
        $ch = $this->get("context.helper");
        
        $causal_array["numeroalumno"] = $ch->parseNumeroAlumno($causal_array["numeroalumno"]);
        $causal_array['curriculum'] = $ch->parseCurriculum($causal_array['curriculum']);
        
        list($year,$semestre) = $ch->getPeriodoAnterior();
        $semestre = array_key_exists("semestre", $causal_array)?$causal_array["semestre"]:$semestre;
        $year = array_key_exists("ano", $causal_array)?$causal_array["ano"]:$year;
        $resolucion_val = array_key_exists("resolucion", $causal_array)?$causal_array["resolucion"]:null;
        /*
        $causal_array['testdeactualidadreprobados'] = $ch->parseNumber($causal_array['testdeactualidadreprobados']);
        $causal_array['testdeactualidadaprobados'] = $ch->parseNumber($causal_array['testdeactualidadaprobados']);
        $causal_array['ppa'] = $ch->parseNumber($causal_array['ppa']);
        $causal_array['creditosinscritos'] = $ch->parseNumber($causal_array['creditosinscritos']);
        $causal_array['creditosaprobados'] = $ch->parseNumber($causal_array['creditosaprobados']);
        $causal_array['creditosconvalidados'] = $ch->parseNumber($causal_array['creditosconvalidados']);
        $causal_array['crdnotap'] = $ch->parseNumber($causal_array['crdnotap']);
        $causal_array['creditosreprobados'] = $ch->parseNumber($causal_array['creditosreprobados']);
        */
        
        $curriculum = $em->getRepository('pDevAlumnosBundle:Curriculum')->findOneByCodigo($causal_array['curriculum']);
        $numeroalumno = $em->getRepository('pDevAlumnosBundle:AlumnoNumero')->findOneByNumeroAlumno($causal_array["numeroalumno"]);
        if($numeroalumno and $curriculum)
        {
        
            $academicos = $em->getRepository('pDevAlumnosBundle:Academicos')->findOneBy(array('alumno'=>$numeroalumno->getId(),'curriculum'=>$curriculum->getId()));
            if($academicos)
            {
                // determinamos la resolucion si es que la contiene
                
                if($resolucion_val)
                {
                    $resolucion_tmp = "";
                    $pos1 = strpos($resolucion_val, 'deseliminado');
                    if($pos1!==false)
                        $resolucion_tmp = 'deseliminado';
                    else
                        $resolucion_tmp = 'eliminado';

                    $pos2 = strpos($resolucion_val, 'gracia');
                    if($pos2!==false)
                        $resolucion_tmp .= '_gracia';
                    
                    $resolucion_val = $resolucion_tmp;
                }
                else
                    $resolucion_val = 'pendiente';
                //print_r($causal_array);
                

                $resolucion = $em->getRepository('pDevCausalesBundle:Resolucion')->findOneByIdentificador($resolucion_val);
                
                $causal = $em->getRepository('pDevCausalesBundle:Causal')->findOneBy(array('year'=>$year,'semestre'=>$semestre,'academicos'=>$academicos->getId()));
                if(!$causal)
                {
                    $causal = new Causal();
                    $causal->setYear($year);
                    $causal->setSemestre($semestre);
                    $causal->setAcademicos($academicos);

                    $academicos->addCausale($causal);
                    $em->persist($causal);
                    
                    
                }
                $causal->setNumeroCausal(-1);
                
                $ah = $this->get("alumnos.helper");
                
                $taaprobados = $ah->getTestActualidadAprobados($numeroalumno->getNumeroAlumno(),$year,$semestre);
                $causal->setActualidadAprobados($taaprobados);
                
                $tareprobados = $ah->getTestActualidadReprobados($numeroalumno->getNumeroAlumno(),$year,$semestre);
                $causal->setActualidadReprobados($tareprobados);
                

                $causal->setResolucion($resolucion);
                if($resolucion_val == 'pendiente')
                    $causal->setAbierta(true);
                else
                    $causal->setAbierta(false);    

                $causal->setCreditosAprobados($academicos->getAntecedentesRendimiento()->getCratot());
                $causal->setSemestresCursados($academicos->getAntecedentesRendimiento()->getSemestresCursados());
                $causal->setPpa($academicos->getAntecedentesRendimiento()->getPpa());
                
                
                foreach($causal_array['articulos'] as $art)
                {
                    $artid = $art['nombre'];
                    $articulo_tipo =  $em->getRepository('pDevCausalesBundle:ArticuloTipo')->findOneByNombre($artid);
                    if(!$articulo_tipo)
                    {
                        $articulo_tipo = new ArticuloTipo();
                        $articulo_tipo->setNombre($artid);
                        $descripcion = null;
                        if($artid=='30a')
                            $descripcion = "El alumno que a contar del término de su tercer período académico, no mantenga un promedio ponderado acumulado igual o superior a cuatro (4.0)";
                        if($artid=='30b')
                            $descripcion = "El alumno que al término de sus dos primeros períodos académicos cursados, considerados en conjunto, no apruebe 60 créditos mínimos y optativos.";
                        if($artid=='30c')
                            $descripcion = "El alumno que fuere reprobado en tres oportunidades en un mismo curso";
                        if($artid=='30d')
                            $descripcion = "El alumno que reprobare tres cursos distintos, en dos oportunidades cada uno de ellos.";
                        if($artid=='30e')
                            $descripcion = "El alumno que desde su tercer semestre de permanencia en su programa de estudios conducente a un título o grado académico reprobare un número superior a 5 cursos o actividades del currículo mínimo o complementario.";
                        
                        $articulo_tipo->setDescripcion($descripcion);
                        $em->persist($articulo_tipo);
                    }
                    
                    $articulo =  $em->getRepository('pDevCausalesBundle:Articulo')->findOneBy(array('causal'=>$causal->getId(),'articuloTipo'=>$articulo_tipo->getId()));
                    if(!$articulo)
                    {
                        $articulo = new Articulo();
                        $articulo->setArticuloTipo($articulo_tipo);
                        $articulo->setCausal($causal);
                        $causal->addArticulo($articulo);
                        $em->persist($articulo);
                    }
                        
                    if($artid=='30b')
                    {
                        $articulo->setCreditosExigidos($ch->parseNumber($art["cr.exigido"]));
                        
                    }
                    
                    
                    foreach($art['cursos'] as $curso)
                    {
                        $sigla = $ch->parseSigla($curso["sigla"]);

                        $articuloCurso =  $em->getRepository('pDevCausalesBundle:ArticuloCurso')->findOneBy(array('articulo'=>$articulo->getId(),'sigla'=>$sigla));

                        if(!$articuloCurso)
                        {
                            $nombre = '';
                            $ccurso = $em->getRepository('pDevCursosBundle:Curso')->findOneBy(array('sigla'=>$sigla));
                            if($ccurso)
                            {
                                $nombre = $ccurso->getNombre();
                            }

                            $articuloCurso = new ArticuloCurso();
                            $articuloCurso->setSigla($sigla);
                            $articuloCurso->setNombre($nombre);
                            
                            $articuloCurso->setArticulo($articulo);
                            $em->persist($articuloCurso);
                        
                        }
                        
                        $articuloCurso->setCreditos($curso["cred"]);
                        
                        

                        foreach($curso['notas'] as $nota)
                        {
                            $nota_sem = $ch->parseSemestre($nota["sem"]);
                            $nota_ano = $ch->parseNumber($nota["ano"]);
                            
                            $articuloCursoNota =  $em->getRepository('pDevCausalesBundle:ArticuloCursoNota')->findOneBy(array('articuloCurso'=>$articuloCurso->getId(),'semestre'=>$nota_sem,'year'=>$nota_ano));
                            if(!$articuloCursoNota)
                            {
                                $articuloCursoNota = new ArticuloCursoNota();
                                $articuloCursoNota->setSemestre($nota_sem);
                                $articuloCursoNota->setYear($nota_ano);

                                $articuloCursoNota->setArticuloCurso($articuloCurso);

                                $em->persist($articuloCursoNota);
                            }
                            $articuloCursoNota->setNota($ch->parseNumber($nota["nota"]));
                            
                            if(array_key_exists('tipo', $nota))
                                $articuloCursoNota->setTipo($nota["tipo"]);
                        }


                    }
                    
                    
                    
                    $em->flush();
                    
                    
                }
                
                
                // recontabilizamos las causales para este alumno
                $qb = $em->getRepository('pDevCausalesBundle:Causal')->createQueryBuilder('i');
                $causales = $qb->leftJoin('i.academicos','ac')
                            ->leftJoin('ac.alumno','na')
                            ->leftJoin('i.resolucion','res')
                            ->where('na.numeroAlumno = :numAl and i.resolucion')
                            ->andWhere('res.identificador != :resolucion')
                            ->setParameter('numAl', $numeroalumno->getNumeroAlumno())
                            ->setParameter('resolucion', 'invalida')
                            ->orderBy('i.year', 'ASC')
                            ->addOrderBy('i.semestre', 'ASC')
                            ->getQuery()
                            ->getResult();
                
                
                foreach($causales as $causal)
                {
                    $causal_year = $causal->getYear();
                    $causal_sem = $causal->getSemestre();
                    $causales_count = $ah->getNumeroCausal($numeroalumno->getNumeroAlumno(),$causal_year,$causal_sem);
                    $causal->setNumeroCausal($causales_count);    
                    $em->persist($causal);
                }
                $em->flush();
                
            }
            else
                return "no se encontró el curriculum \"".$causal_array["curriculum"]."' para el numero de alumno '".$causal_array["numeroalumno"]."'";
        }
        else
            return "no se encontró numero de alumno: \'".$causal_array["numeroalumno"]."\'";
        
        return "ok";
    }
}
