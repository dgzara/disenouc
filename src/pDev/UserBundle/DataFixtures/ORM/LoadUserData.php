<?php
namespace pDev\UserBundle\DataFixtures\ORM;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use pDev\UserBundle\Entity\User;
use pDev\UserBundle\Entity\Funcionario;
use pDev\UserBundle\Entity\Alumno;
use pDev\UserBundle\Entity\Profesor;
use pDev\PracticasBundle\Entity\Supervisor;
use pDev\PracticasBundle\Entity\Contacto;
use pDev\UserBundle\Entity\Permiso;

class LoadUserData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;

	public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
    
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $userpersona = $manager->getRepository('pDevUserBundle:Funcionario')->findOneByRut("166284740");
        if(!$userpersona)
            $userPersona = new Funcionario();
        $userPersona->setNombres("María Rosa");
        $userPersona->setApellidoPaterno("Domper");
        $userPersona->setApellidoMaterno("Rodríguez");
        $userPersona->setEmail("coordinador@uc.cl");
        $userPersona->setRut("101940020");
        
        $manager->persist($userPersona);
        
        $userAdmin = $manager->getRepository('pDevUserBundle:User')->findOneByUsername("dlgomez@uc.cl");
        if(!$userAdmin)
            $userAdmin = new User();
        $userAdmin->setUsername('coordinador@uc.cl');
        $userAdmin->setPlainPassword('coordinador');
        $userAdmin->setEmail('coordinador@uc.cl');
        $userAdmin->setEnabled(true);
        $userAdmin->addPersona($userPersona);
        $userAdmin->setNombres("María Rosa");
        $userAdmin->setApellidoPaterno("Rodríguez");
        $userAdmin->setApellidoMaterno("");
        $userAdmin->setRut("101940020");
        $userPersona->setUsuario($userAdmin);
        $userAdmin->addRole('ROLE_SUPER_ADMIN');
        
        $this->createPermisos($userAdmin, $manager);
        
        $manager->persist($userAdmin);
        
        
        if($this->container->getParameter('fixtures') == 'si')
		{
		    // Profesor
		    $persona1 = new Profesor();
            $persona1->setNombres("Eduardo");
            $persona1->setApellidoPaterno("Allard");
            $persona1->setApellidoMaterno("Fuentes");
            $persona1->setEmail("profesor@uc.cl");
            $persona1->setRut("75461240");
		    $manager->persist($persona1);
		    
		    $userProfesor = new User();
            $userProfesor->setUsername('profesor@uc.cl');
            $userProfesor->setPlainPassword('profesor');
            $userProfesor->setEmail('profesor@uc.cl');
            $userProfesor->setEnabled(true);
            $userProfesor->addPersona($persona1);
            $userProfesor->setNombres("Eduardo");
            $userProfesor->setApellidoPaterno("Allard");
            $userProfesor->setApellidoMaterno("Fuentes");
            $userProfesor->setRut("75461240");
            $persona1->setUsuario($userProfesor);
            $userProfesor->addRole('ROLE_USER');
            $manager->persist($userProfesor);
            
		    // Alumno 1
		    $persona2 = new Alumno();
            $persona2->setNombres("Valentina");
            $persona2->setApellidoPaterno("Urbina");
            $persona2->setApellidoMaterno("Pérez");
            $persona2->setEmail("alumno@uc.cl");
            $persona2->setRut("173435141");
            $persona2->setNumeroAlumno("06629105");
		    $manager->persist($persona2);
		    
		    $userAlumno = new User();
            $userAlumno->setUsername('alumno@uc.cl');
            $userAlumno->setPlainPassword('alumno');
            $userAlumno->setEmail('alumno@uc.cl');
            $userAlumno->setEnabled(true);
            $userAlumno->addPersona($persona2);
            $userAlumno->setNombres("Valentina");
            $userAlumno->setApellidoPaterno("Urbina");
            $userAlumno->setApellidoMaterno("Pérez");
            $userAlumno->setRut("173435141");
            $persona2->setUsuario($userAlumno);
            $userAlumno->addRole('ROLE_USER');
            $manager->persist($userAlumno);
            
            // Alumno 2
		    $persona5 = new Alumno();
            $persona5->setNombres("Eduardo");
            $persona5->setApellidoPaterno("Torres");
            $persona5->setApellidoMaterno("Pérez");
            $persona5->setEmail("alumno2@uc.cl");
            $persona5->setRut("166249140");
            $persona5->setNumeroAlumno("08439510");
		    $manager->persist($persona5);
		    
		    $userAlumno2 = new User();
            $userAlumno2->setUsername('alumno2@uc.cl');
            $userAlumno2->setPlainPassword('alumno');
            $userAlumno2->setEmail('alumno2@uc.cl');
            $userAlumno2->setEnabled(true);
            $userAlumno2->addPersona($persona5);
            $userAlumno2->setNombres("Valentina");
            $userAlumno2->setApellidoPaterno("Urbina");
            $userAlumno2->setApellidoMaterno("Pérez");
            $userAlumno2->setRut("173435141");
            $persona5->setUsuario($userAlumno2);
            $userAlumno2->addRole('ROLE_USER');
            $manager->persist($userAlumno2);
            
		    // Supervisor
		    $persona3 = new Supervisor();
            $persona3->setNombres("Juan");
            $persona3->setApellidoPaterno("Henríquez");
            $persona3->setApellidoMaterno("Fuentes");
            $persona3->setEmail("supervisor@empresa.cl");
            $persona3->setRut("103451451");
            $persona3->setCargo("Jefe de proyecto");
		    $manager->persist($persona3);
		    
		    $userSupervisor = new User();
            $userSupervisor->setUsername('supervisor@uc.cl');
            $userSupervisor->setPlainPassword('supervisor');
            $userSupervisor->setEmail('supervisor@empresa.cl');
            $userSupervisor->setEnabled(true);
            $userSupervisor->addPersona($persona3);
            $userSupervisor->setNombres("Juan");
            $userSupervisor->setApellidoPaterno("Henríquez");
            $userSupervisor->setApellidoMaterno("Fuentes");
            $userSupervisor->setRut("103451451");
            $persona3->setUsuario($userSupervisor);
            $userSupervisor->addRole('ROLE_USER');
            $manager->persist($userSupervisor);
            
		    // Contacto
		    $persona4 = new Contacto();
            $persona4->setNombres("Tomás");
            $persona4->setApellidoPaterno("Echeverría");
            $persona4->setApellidoMaterno("Fuentes");
            $persona4->setEmail("contacto@empresa.com");
            $persona4->setRut("85423215");
		    $manager->persist($persona4);
		    
		    $userContacto = new User();
            $userContacto->setUsername('contacto@empresa.cl');
            $userContacto->setPlainPassword('contacto');
            $userContacto->setEmail('contacto@empresa.cl');
            $userContacto->setEnabled(true);
            $userContacto->addPersona($persona4);
            $userContacto->setNombres("Tomás");
            $userContacto->setApellidoPaterno("Echeverría");
            $userContacto->setApellidoMaterno("Fuentes");
            $userContacto->setRut("85423215");
            $persona4->setUsuario($userContacto);
            $userContacto->addRole('ROLE_USER');
            $manager->persist($userContacto);
            
            // Referencias
            $this->addReference('persona-profesor', $persona1);
            $this->addReference('persona-alumno', $persona2);
            $this->addReference('persona-supervisor', $persona3);
            $this->addReference('persona-contacto', $persona4);
            
            $this->addReference('user-profesor', $userProfesor);
            $this->addReference('user-alumno', $userAlumno);
            $this->addReference('user-supervisor', $userSupervisor);
            $this->addReference('user-contacto', $userContacto);
		}
        
        $this->addReference('user_default', $userAdmin);
        
        $manager->flush();
    }
    
    private function createPermisos($user = null, $em)
    {
        if(!$user)
            $user = $this->getUser();
            
        $basic_role = $em->getRepository('pDevUserBundle:Role')->findOneByOrden(0);
        $sites = $em->getRepository('pDevUserBundle:Sitio')->findAll();
        
        foreach($sites as $site)
        {
            $permiso = $em->getRepository('pDevUserBundle:Permiso')->findOneBy(array('user'=>$user->getId(),'site'=>$site->getId()));
            if(!$permiso)
            {
                $permiso = new Permiso();
                $permiso->setSite($site);
                $permiso->setRole($basic_role);
                $permiso->setUser($user);
                $em->persist($permiso);
            }
        }
    }
    
    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 2; // the order in which fixtures will be loaded
    }
}
