<?php
namespace pDev\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use pDev\UserBundle\Entity\PreguntaFrecuente;

class LoadPreguntasFrecuentesData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $pregunta1 = new PreguntaFrecuente();
        $pregunta1->setPregunta('¿Cómo me consigo la práctica?');
        $pregunta1->setTexto('<p>El estudiante podrá acceder a ofertas de prácticas ingresando a la plataforma web. Además, el estudiante puede conseguir prácticas por contactos propios. Para la práctica de Oficina, el lugar debe tener como mínimo 5 años de antigüedad y estar conformado por un equipo de al menos 5 personas.</p>');
        $pregunta1->setOrden(0);
        $manager->persist($pregunta1);
        
        $pregunta2 = new PreguntaFrecuente();
        $pregunta2->setPregunta('¿Cuándo puedo hacer mis prácticas?');
        $pregunta2->setTexto('<ul>
<li>OFICINA· Una vez completado el 5to semestre de la carrera.</li>
<li>SERVICIO · Una vez completado el 7mo semestre de la carrera.</li>
</ul>
<p>OJO: Cuando llegues a título, tienes que tener ambas prácticas cursadas e informes entregados</p>');
        $pregunta2->setOrden(1);
        $manager->persist($pregunta2);
        
        $pregunta3 = new PreguntaFrecuente();
        $pregunta3->setPregunta('¿Cuál es la duración de la práctica?');
        $pregunta3->setTexto('<p>La duración es de 240 horas en jornadas de un mínimo de 4 y no mayor de 8 horas presenciales y un mínimo de 2 días a la semana.</p>');
        $pregunta3->setOrden(2);
        $manager->persist($pregunta3);
        
        $pregunta4 = new PreguntaFrecuente();
        $pregunta4->setPregunta('¿Cuál es la diferencia entre práctica de oficina y servicio?');
        $pregunta4->setTexto('<ul>
<li>OFICINA · Trabajo de diseño que permite poner en práctica en la realidad, los conocimientos adquiridos, asumir responsabilidades, aprender y aportar en el proceso de diseño.</li>
<li>SERVICIO · Desempeño en diseño que permite hacer un aporte social concreto.</li>
</ul>');
        $pregunta4->setOrden(3);
        $manager->persist($pregunta4);
        
        $pregunta5 = new PreguntaFrecuente();
        $pregunta5->setPregunta('¿Cuándo se inscribe el ramo?');
        $pregunta5->setTexto('<p>El ramo se inscribe una vez completada la experiencia (240 horas)</p>
<p>Se hace la práctica completa y al semestre siguiente se inscribe el ramo para alcanzar a entregar el informe. Cada práctica es un ramo distinto, Práctica de oficina: DNO0422 y Practica de Servicio: DNO0512.</p>
');
        $pregunta5->setOrden(4);
        $manager->persist($pregunta5);
        
        $pregunta6 = new PreguntaFrecuente();
        $pregunta6->setPregunta('¿Cuándo se entrega el informe?');
        $pregunta6->setTexto('<p>El informe se entrega a más tardar 1 semestre a partir de la fecha de término de la práctica.</p>
<p>Para los que ya realizaron la práctica e inscribieron el ramo la fecha de entrega es a mediados de abril (I semestre) y a mediados de septiembre (II semestre).</p>
');
        $pregunta6->setOrden(5);
        $manager->persist($pregunta6);
        $manager->flush();
    }
    
    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 0; // the order in which fixtures will be loaded
    }
}
