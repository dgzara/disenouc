<?php
namespace pDev\CausalesBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use pDev\PracticasBundle\Entity\CriterioTipo;

class LoadCriteriosTipoData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $criterios = array();
        
        /**
         * Profesor Oficina
         */
        
        
        $criterios[] = array(
            'aspecto'       =>  'Contenido',
            'tipoPractica'  =>  'Oficina',
            'tipoEvaluador' =>  'Profesor',
            'nombre'        =>  'Análisis del contexto',
            'descripcion'   =>  'Refiere a la caracterización detallada de la empresa y a la visión estratégica del diseño en el mercado.',
            'explicacion'   =>  'El estudiante presenta en forma detallada, precisa y pertinente la empresa y comunica el rol estratégico del diseñador en el entorno profesional que enfrenta.'
        );
        
        $criterios[] = array(
            'aspecto'       =>  'Contenido',
            'tipoPractica'  =>  'Oficina',
            'tipoEvaluador' =>  'Profesor',
            'nombre'        =>  'Fundamentos en propuestas, resultados y conclusiones',
            'descripcion'   =>  'Refiere al uso de fuentes teóricas y/o procesos de diseño pertinentes para sostener sus propuestas, resultados y conclusiones.', 
            'explicacion'   =>  'Todas las propuestas, resultados y conclusiones se apoyan y fundamentan de manera creativa en antecedentes y procesos de diseño pertinentes y apropiados para el contexto en que se desempeña el estudiante.'
        );
        
        $criterios[] = array(
            'aspecto'       =>  'Contenido',
            'tipoPractica'  =>  'Oficina',
            'tipoEvaluador' =>  'Profesor',
            'nombre'        =>  'Aporte del Diseño',
            'descripcion'   =>  'Refiere al aporte creativo y pertinente del diseño en cualquier etapa del ciclo de vida del producto.',
            'explicacion'   =>  'Las propuestas generadas por el estudiante constituyen un aporte de diseño, producto de la aplicación creativa y pertinente de conocimientos y habilidades aprendidas durante los años de estudio en la Escuela.'
        );
        
        $criterios[] = array(
            'aspecto'       =>  'Contenido',
            'tipoPractica'  =>  'Oficina',
            'tipoEvaluador' =>  'Profesor',
            'nombre'        =>  'Retorno reflexivo',
            'descripcion'   =>  'Refiere a la capacidad de razonar sobre distintos aspectos de su participación en la empresa fundamentando sus opciones y haciéndose cargo de las dificultades evidenciadas.',
            'explicacion'   =>  'El estudiante evalúa su desempeño dando cuenta sus fortalezas, falencias y explicando en detalle las decisiones tomadas. Esto le permite construir un juicio crítico ante la experiencia de diseño y detectar aspectos a mejorar para un desempeño apropiado en el ámbito del diseño.'
        );
        
        $criterios[] = array(
            'aspecto'       =>  'Contenido',
            'tipoPractica'  =>  'Oficina',
            'tipoEvaluador' =>  'Profesor',
            'nombre'        =>  'Logro de objetivos específicos de la práctica',
            'descripcion'   =>  'Refiere al cumplimiento de los objetivos planteados en el plan de práctica y como el alumno es capaz de sortear las restricciones que la realidad profesional le impone. Debe quedar en evidencia a lo largo del documento.',
            'explicacion'   =>  'A lo largo del informe el alumno evidencia cómo cumplió los objetivos establecidos en el plan de práctica y las restricciones que enfrentó, dando cuenta de las estrategias desarrolladas para el logro de estos objetivos.'
        );
        
        $criterios[] = array(
            'aspecto'       =>  'Formal',
            'tipoPractica'  =>  'Oficina',
            'tipoEvaluador' =>  'Profesor',
            'nombre'        =>  'Redacción, gramática, ortografía',
            'descripcion'   =>  'Se refiere a la elaboración de un documento estructurado, comprensible con un manejo del lenguaje y normas de escritura acorde a la formalidad de la entrega.',
            'explicacion'   =>  'El informe presenta una estructura de contenidos clara, respeta las normas de escritura, mantiene un lenguaje formal y es interesante y fácil de leer.'
        );
        
        $criterios[] = array(
            'aspecto'       =>  'Formal',
            'tipoPractica'  =>  'Oficina',
            'tipoEvaluador' =>  'Profesor',
            'nombre'        =>  'Claridad y precisión de infografías y gráficos',
            'descripcion'   =>  'Se refiere al buen diseño y a la diagramación de textos, gráficos e infografías que aclaran y aportan a la comprensión del lector.',
            'explicacion'   =>  'Existen una diagramación clara y de calidad en textos, infografías explicativas e imágenes que enriquecen y aportan al entendimiento del lector.'
        );
        
        /**
         * Profesor servicio
         */
        
        $criterios[] = array(
            'aspecto'       =>  'Contenido',
            'tipoPractica'  =>  'Servicio',
            'tipoEvaluador' =>  'Profesor',
            'nombre'        =>  'Análisis del contexto y características de la práctica',
            'descripcion'   =>  'Refiere a la caracterización detallada del entorno social donde se desenvuelve el estudiante y la visión estratégica del valor del diseño en la realidad social.',
            'explicacion'   =>  'El estudiante presenta en forma detallada, precisa y pertinente al usuario y su entorno social. Comunica claramente el rol del diseñador en el entorno social que enfrenta.'
        );
        
        $criterios[] = array(
            'aspecto'       =>  'Contenido',
            'tipoPractica'  =>  'Servicio',
            'tipoEvaluador' =>  'Profesor',
            'nombre'        =>  'Fundamentos en propuestas, resultados y conclusiones',
            'descripcion'   =>  'Refiere al uso de fuentes teóricas e instrumentos de diseño pertinentes para sostener sus propuestas, resultados y conclusiones.',
            'explicacion'   =>  'Todas las propuestas, resultados y conclusiones se apoyan y fundamentan de manera creativa en antecedentes teóricos y/o procesos de diseño pertinentes y apropiados para el contexto social en que se desempeña el estudiante.'
        );
        
        $criterios[] = array(
            'aspecto'       =>  'Contenido',
            'tipoPractica'  =>  'Servicio',
            'tipoEvaluador' =>  'Profesor',
            'nombre'        =>  'Impacto del Diseño',
            'descripcion'   =>  'Se refiere a la influencia y aporte de las soluciones desarrolladas por el estudiante en práctica en el beneficio social de las personas y su entorno.',
            'explicacion'   =>  'Las propuestas generadas por el estudiante constituyen un impacto positivo, fruto de la aplicación creativa y pertinente de conocimientos y habilidades aprendidas durante los años de estudio en la Escuela.'
        );
        
        $criterios[] = array(
            'aspecto'       =>  'Contenido',
            'tipoPractica'  =>  'Servicio',
            'tipoEvaluador' =>  'Profesor',
            'nombre'        =>  'Retorno reflexivo',
            'descripcion'   =>  'Refiere a la capacidad de razonar sobre distintos aspectos de su participación en la realidad social fundamentando sus opciones y haciéndose cargo de las dificultades evidenciadas.',
            'explicacion'   =>  'El estudiante evalúa su experiencia, dando cuenta de sus fortalezas y falencias y explicando en detalle las decisiones tomadas. Esto le permite construir un juicio crítico del rol social del diseño y los aspectos a mejorar en su formación para un desempeño responsable en la realidad.'
        );
        
        $criterios[] = array(
            'aspecto'       =>  'Contenido',
            'tipoPractica'  =>  'Servicio',
            'tipoEvaluador' =>  'Profesor',
            'nombre'        =>  'Logro de objetivos específicos de la práctica',
            'descripcion'   =>  'Refiere al cumplimiento de los objetivos específicos planteados en el plan de práctica. Estos se evidencia a lo largo del documento, en la capacidad de sortear las restricciones de la realidad social.',
            'explicacion'   =>  'A lo largo del informe el alumno evidencia cómo cumplió los objetivos establecidos en el plan de práctica y las restricciones que enfrentó, dando cuenta de las estrategias desarrolladas para el logro de estos objetivos.'
        );
        
        $criterios[] = array(
            'aspecto'       =>  'Formal',
            'tipoPractica'  =>  'Servicio',
            'tipoEvaluador' =>  'Profesor',
            'nombre'        =>  'Redacción, gramática y ortografía',
            'descripcion'   =>  'Se refiere a la elaboración de un documento bien estructurado en cuanto a contenido, al manejo del lenguaje y las normas de escritura acorde a la formalidad de la entrega.',
            'explicacion'   =>  'El informe presenta una estructura de contenidos clara, respeta las normas de escritura, mantiene un lenguaje formal y es interesante y fácil de leer.'
        );
        
        $criterios[] = array(
            'aspecto'       =>  'Formal',
            'tipoPractica'  =>  'Servicio',
            'tipoEvaluador' =>  'Profesor',
            'nombre'        =>  'Claridad y calidad de diagramación',
            'descripcion'   =>  'Se refiere al buen diseño y diagramación de textos, infografías, imágenes y gráficos que apoyan, aclaran y aportan a la comprensión del lector.',
            'explicacion'   =>  'Existen una diagramación clara y de calidad en textos, infografías explicativas e imágenes que enriquecen y aportan al entendimiento del lector.'
        );
        
        /**
         * Supervisor Oficina
         */
        
        $criterios[] = array(
            'aspecto'       =>  'Practicante',
            'tipoPractica'  =>  'Oficina',
            'tipoEvaluador' =>  'Supervisor',
            'nombre'        =>  'Desempeño',
            'descripcion'   =>  'Refiere a la capacidad de aplicar sus competencias a la resolución creativa, efectiva, pertinente y oportuna de los desafíos que se le proponen en la empresa.',
            'explicacion'   =>  'Durante toda la práctica el estudiante genera gran cantidad iniciativas de alta calidad, resultando en una mejora sustantiva de los procesos que se le asignan y que no habrían sido posibles sin sus aportes.'
        );
        
        $criterios[] = array(
            'aspecto'       =>  'Practicante',
            'tipoPractica'  =>  'Oficina',
            'tipoEvaluador' =>  'Supervisor',
            'nombre'        =>  'Conocimientos',
            'descripcion'   =>  'Refiere al dominio de conceptos, procedimientos y técnicas propias de la especialidad y requeridas por la situación laboral.',
            'explicacion'   =>  'Durante toda la práctica el estudiante demuestra un ejemplar dominio de conceptos, procedimientos y técnicas requeridas por la situación laboral.'
        );
        
        $criterios[] = array(
            'aspecto'       =>  'Practicante',
            'tipoPractica'  =>  'Oficina',
            'tipoEvaluador' =>  'Supervisor',
            'nombre'        =>  'Iniciativa',
            'descripcion'   =>  'Refiere a la capacidad de proponer ideas, detectar oportunidades y soluciones a situaciones de manera espontánea y sin la necesidad de solicitarle explícitamente.',
            'explicacion'   =>  'Durante toda la práctica el estudiante propone ideas, detecta oportunidades y soluciones demostrando interés y proactividad en su desempeño.'
        );
        
        $criterios[] = array(
            'aspecto'       =>  'Practicante',
            'tipoPractica'  =>  'Oficina',
            'tipoEvaluador' =>  'Supervisor',
            'nombre'        =>  'Responsabilidad profesional',
            'descripcion'   =>  'Refiere al cumplimiento de proyectos y responsabilidades acordados y respeto por los códigos profesionales y éticos de la situación laboral.',
            'explicacion'   =>  'Durante toda la práctica el estudiante demuestra un profundo conocimientos de sus responsabilidades, cumplimiento de horario y entregas de trabajos, entre otros, desempeñándose de manera intachable de acuerdo con estos criterios.'
        );
        
        $criterios[] = array(
            'aspecto'       =>  'Practicante',
            'tipoPractica'  =>  'Oficina',
            'tipoEvaluador' =>  'Supervisor',
            'nombre'        =>  'Trabajo de Equipo',
            'descripcion'   =>  'Refiere a la comprensión e interés por participar activamente en las tareas de equipo, articulando aportes individuales para el logro de un objetivo común.',
            'explicacion'   =>  'Durante toda la práctica la participación del estudiante en labores de equipo destaca por su contribución y aporte al logro de objetivos comunes, adaptándose con facilidad al equipo.'
        );
        
        $criterios[] = array(
            'aspecto'       =>  'Practicante',
            'tipoPractica'  =>  'Oficina',
            'tipoEvaluador' =>  'Supervisor',
            'nombre'        =>  'Habilidades comunicativas',
            'descripcion'   =>  'Refiere a la capacidad de comunicar y dar a entender de manera efectiva ideas, conceptos y puntos de vista, defender y presentar propuestas.',
            'explicacion'   =>  'Durante toda la práctica el estudiante se comunica con facilidad con distintas audiencias y genera resultados que demuestran un excelente dominio del diseño y el lenguaje (formal, técnico e informal).'
        );
        
        /**
         * Supervisor Servicio
         */
        
        $criterios[] = array(
            'aspecto'       =>  'Practicante',
            'tipoPractica'  =>  'Servicio',
            'tipoEvaluador' =>  'Supervisor',
            'nombre'        =>  'Desempeño',
            'descripcion'   =>  'Refiere a la capacidad de aplicar sus competencias a la resolución autónoma creativa, efectiva, pertinente y oportuna de las necesidades del usuario a beneficiar.',
            'explicacion'   =>  'Durante toda la práctica el estudiante genera gran cantidad iniciativas de alta calidad, resultando en una mejora sustantiva de las necesidades del usuario o el entorno social y que no habrían sido posibles sin su aporte.'
        );
        
        $criterios[] = array(
            'aspecto'       =>  'Practicante',
            'tipoPractica'  =>  'Servicio',
            'tipoEvaluador' =>  'Supervisor',
            'nombre'        =>  'Conocimientos',
            'descripcion'   =>  'Refiere al dominio de conceptos, procedimientos y técnicas propias de la especialidad y requeridas por el usuario y su entorno social específico.',
            'explicacion'   =>  'Durante toda la práctica el estudiante demuestra un ejemplar dominio de conceptos, procedimientos y técnicas requeridas por las necesidades específicas del usuario y o el entorno social que enfrenta.'
        );
        
        $criterios[] = array(
            'aspecto'       =>  'Practicante',
            'tipoPractica'  =>  'Servicio',
            'tipoEvaluador' =>  'Supervisor',
            'nombre'        =>  'Iniciativa y liderazgo',
            'descripcion'   =>  'Refiere a la capacidad de detección de oportunidades y propuesta de soluciones a necesidades del usuario y/o su entorno social de manera autónoma y autogestionada.',
            'explicacion'   =>  'Durante toda la práctica el estudiante, detecta oportunidades, propone soluciones y nuevas tareas de su parte, demostrando interés y proactividad en su desempeño.'
        );
        
        $criterios[] = array(
            'aspecto'       =>  'Practicante',
            'tipoPractica'  =>  'Servicio',
            'tipoEvaluador' =>  'Supervisor',
            'nombre'        =>  'Responsabilidad social',
            'descripcion'   =>  'Refiere al entendimiento y aplicacion del diseño como disciplina al servicio de las personas, centrada en la interacción del ser humano y su entorno.',
            'explicacion'   =>  'Durante toda la práctica el estudiante demuestra un profundo entendimiento de sus rol y responsabilidad ante el usuario y el entorno social, desempeñándose de manera acorde.'
        );
        
        $criterios[] = array(
            'aspecto'       =>  'Practicante',
            'tipoPractica'  =>  'Servicio',
            'tipoEvaluador' =>  'Supervisor',
            'nombre'        =>  'Trabajo de Equipo',
            'descripcion'   =>  'Refiere a la comprensión e interés por participar activamente en las tareas de equipo, articulando aportes individuales para el logro de un objetivo común.',
            'explicacion'   =>  'Durante toda la práctica la participación del estudiante en labores de equipo destaca por su contribución y aporte al logro de objetivos comunes, adaptándose con facilidad al equipo.'
        );
        
        $criterios[] = array(
            'aspecto'       =>  'Practicante',
            'tipoPractica'  =>  'Servicio',
            'tipoEvaluador' =>  'Supervisor',
            'nombre'        =>  'Habilidades comunicativas',
            'descripcion'   =>  'Refiere a la capacidad de comunicar y dar a entender de manera efectiva ideas, conceptos y puntos de vista.',
            'explicacion'   =>  'Durante toda la práctica el estudiante se comunica con facilidad con distintas audiencias y genera documentos que demuestran un excelente dominio del diseño y el lenguaje (formal, técnico e informal).'
        );
        
        foreach($criterios as $criterio_array)
        {
            
            $criterio = new CriterioTipo();
            $criterio->setAspecto($criterio_array['aspecto']);
            $criterio->setNombre($criterio_array['nombre']);
            $criterio->setDescripcion($criterio_array['descripcion']);
            $criterio->setExplicacion($criterio_array['explicacion']);
            $criterio->setTipoEvaluador($criterio_array['tipoEvaluador']);
            $criterio->setTipoPractica($criterio_array['tipoPractica']);
            
            $manager->persist($criterio);
        }
        
        $manager->flush();
    }
    
    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 8; // the order in which fixtures will be loaded
    }
}
