<?php

namespace pDev\ParserBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SyncCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('parser:sync')
            ->setDescription('Sincronizar')
            ->addArgument(
                'year',
                InputArgument::REQUIRED,
                'Who do you want to greet?'
            )
            ->addArgument(
                'semestre',
                InputArgument::REQUIRED,
                'Who do you want to greet?'
            )
            ->addArgument(
                'page',
                InputArgument::REQUIRED,
                'Who do you want to greet?'
            )
            ->addOption(
               'yell',
               null,
               InputOption::VALUE_NONE,
               'If set, the task will yell in uppercase letters'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ch = $this->getContainer()->get('context.helper');
        $pc = $this->getContainer()->get('pdev.sync.controller');
        
        $year = $input->getArgument('year');
        $sem = $input->getArgument('semestre');
        $page = $input->getArgument('page');
        
        //validamos parametros
        $mensaje = $pc->validaParams($year,$sem,$page);
        if($mensaje!="ok")
        {
            $response = "'status'=>'error','msg' => $mensaje,'semestre'=>$sem,'year'=>$year,'importados'=>0,'pagina'=>$page";
                $output->writeln($response);
                return;
        }
        
        // creamos cliente y nos logeamos
        $client = $pc->getSgadClient();
        $logged = $pc->getLoggedCrawler($client);
        $crawler = $logged[0];
        if($crawler==null)
        {
            $response = "'status'=>'error','msg' => 'no se pudo iniciar sesion en la plataforma remota','semestre'=>$sem,'year'=>$year,'importados'=>0,'pagina'=>$page,'retries'=>$logged[1]";
                $output->writeln($response);
                return;
        }
        
        // vamos a busqueda masiva y asignamos parametros de busqueda
        $crawler = $client->click($crawler->filter('a:contains("Alumnos")')->link());
        $crawler = $client->click($crawler->filter('a:contains("Búsqueda Masiva")')->link());
        
        $form = $crawler->filter('form[name=formulario]')->form(array(
            'unidad_academica'  => '28',
            'nombre_unidad_academica'  => 'Comunicaciones',
            'niveles'  => '0,',
            'nombre_niveles'  => 'Pregrado,',
            'carreras'  => '2800,2890,',
            'nombre_carreras'  => 'Comunicaciones,Lic para Periodistas,',
            'curriculums'  => '-2',
            'nombre_curriculums'  => 'Todo,',
            'via_ingresos'  => '-2',
            'nombre_via_ingresos'  => 'Todo,'            
            ));
        

        $form['desde']->setValue($year);
        $form['hasta']->setValue($year);
        
        $sem = intval($sem);
        if($sem==1)
        {
            $form['primer_semestre_ing_nombre']->setValue('1° Semestre');
            $form['primer_semestre_ing']->tick();
        }
        else
        {
            $form['segundo_semestre_ing_nombre']->setValue('2° Semestre');
            $form['segundo_semestre_ing']->tick();
        }
        
        $crawler = $client->submit($form);
        
        // calculamos cantidad de resultados y pagina a extraer
        $resultados_count = $pc->getLastIndex($crawler,$pc->getTableAlumnosCrawler($crawler));
        
        $resultados_desde = ($page - 1)*20;
        $resultados_hasta = $page*20;
        if($resultados_count==0)
        {
            $response = "'status'=>'ok:finalizado','msg' => 'no hay resultados','semestre'=>$sem,'year'=>$year,'importados'=>0,'pagina'=>$page,'desde'=>$resultados_desde,'hasta'=>$resultados_hasta,'resultados'=>$resultados_count,'retries'=>$logged[1]";
                $output->writeln($response);
                return;
        }
        if($resultados_hasta>$resultados_count)
        {
            $resultados_hasta = $resultados_count;
            if($resultados_desde>$resultados_hasta)
            {
                $response = "'status'=>'ok:finalizado','msg' => 'pagina no valida','semestre'=>$sem,'year'=>$year,'importados'=>0,'pagina'=>$page,'desde'=>$resultados_desde,'hasta'=>$resultados_hasta,'resultados'=>$resultados_count,'retries'=>$logged[1]";
                $output->writeln($response);
                return;
            }
        }
        
        // vamos a definir mas parametros para mas datos, y buscamos
        $form = $crawler->filter('form[name=formulario]')->form(array(
            'SUBACCION' => 'mas_datos_bm' 
        ));
        
        $crawler = $client->submit($form);
        
        $form = $crawler->filter('form[name=formulario]')->form(array(
            'basico_dir_prin' => '1',
            'basico_dir_otra' => '0',
            'paginaciondesde' => $resultados_desde,
            'paginacionhasta' => $resultados_hasta,
            'numeroSeleccionado' => $page
        ));
        
        //personales
        $form['basico_telefono']->tick();
        $form['basico_sexo']->tick();
        $form['basico_fech_naci']->tick();
        $form['basico_pai_orig']->tick();
        $form['basico_foto']->tick();
        
        //acadingreso
        $form['basico_ppa_psu']->tick();
        $form['basico_colg_procede']->tick();
        $form['basico_egre_ens_media']->tick();
        $form['basico_prefe_post']->tick();
        
        //socioeconomico
        $form['basico_niv_edu_pap']->tick();
        $form['basico_vive_padre']->tick();
        $form['basico_con_quin_vive']->tick();
        $form['basico_prev_salud']->tick();
        
        //rendimiento
        $form['basico_prom_pond']->tick();
        $form['basico_cred_aprobado']->tick();
        $form['basico_cred_convalidado']->tick();
        $form['basico_sems_cursa']->tick();
        $form['basico_cred_inscri']->tick();
        $form['basico_prio_aca_inscrip']->tick();
        $form['basico_providen_bachille']->tick();
        
        $crawler = $client->submit($form);
        
        // buscamos la tabla de resultados
        $table = $pc->getTableAlumnosCrawler($crawler);
        
        // parseamos la tabla y obtenemos un arreglo con los alumnos
        $alumnos = $pc->parseTableAlumnos($table);
        
        // creamos las entidades
        $counter=0;
        foreach($alumnos as $alumno)
        {
            $result = $pc->createAlumno($alumno);
            if($result=="ok")
                $counter++;
            else
            {
                $response = "'status'=>'error','msg' => 'al importar alumnos: '.$result,'semestre'=>$sem,'year'=>$year,'importados'=>$counter,'pagina'=>$page,'desde'=>$resultados_desde,'hasta'=>$resultados_hasta,'resultados'=>$resultados_count,'retries'=>$logged[1]";
                $output->writeln($response);
                return;
            }                
        }
        
        $msgokpag = 'ok:pagina';
        if($resultados_count==$resultados_hasta)
            $msgokpag = 'ok:finalizado';
            
        $response = "'status'=>$msgokpag,'msg' => 'ok','semestre'=>$sem,'year'=>$year,'importados'=>$counter,'pagina'=>$page,'desde'=>$resultados_desde,'hasta'=>$resultados_hasta,'resultados'=>$resultados_count,'retries'=>$logged[1]";

        $output->writeln($response);
    }
}
