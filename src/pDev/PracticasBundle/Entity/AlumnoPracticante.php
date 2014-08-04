<?php

namespace pDev\PracticasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AlumnoPracticante
 *
 * @ORM\Table(name="nb_practicas_practicante")
 * @ORM\Entity
 */
class AlumnoPracticante
{
    const ESTADO_PENDIENTE = "estado.pendiente";
    const ESTADO_ENVIADA = "estado.enviada";
    const ESTADO_APROBADA = "estado.aprobada";
    const ESTADO_RECHAZADA = "estado.rechazada";
    const ESTADO_ACEPTADA_ALUMNO = "estado.aceptada.alumno";
    const ESTADO_ACEPTADA_SUPERVISOR = "estado.aceptada.supervisor";
    const ESTADO_ACEPTADA = "estado.aceptada";
    const ESTADO_INICIADA = "estado.iniciada";
    const ESTADO_TERMINADA = "estado.terminada";
    const ESTADO_INFORME = "estado.informe";
    const ESTADO_EVALUADA = "estado.evaluada";
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="pDev\PracticasBundle\Entity\ProfesorEvaluador", inversedBy="practicantes")
     * @ORM\JoinColumn(name="profesor_id", referencedColumnName="id", nullable=true)
     */
    private $profesor;
    
    /**
     * @ORM\ManyToOne(targetEntity="pDev\PracticasBundle\Entity\Supervisor", inversedBy="practicantes")
     * @ORM\JoinColumn(name="supervisor_id", referencedColumnName="id", nullable=true)
     */
    private $supervisor;
    
    /**
     * @ORM\ManyToOne(targetEntity="pDev\PracticasBundle\Entity\EvaluacionProfesor", inversedBy="practicantes")
     * @ORM\JoinColumn(name="evaluacion_profesor_id", referencedColumnName="id", nullable=true)
     */
    private $profesorEvaluacion;
    
    /**
     * @ORM\ManyToOne(targetEntity="pDev\PracticasBundle\Entity\EvaluacionSupervisor", inversedBy="practicantes")
     * @ORM\JoinColumn(name="evaluacion_supervisor_id", referencedColumnName="id", nullable=true)
     */
    private $supervisorEvaluacion;
    
    /**
     * @ORM\ManyToOne(targetEntity="pDev\UserBundle\Entity\Alumno")
     * @ORM\JoinColumn(name="alumno_id", referencedColumnName="id", nullable=false)
     */
    private $alumno;
    
    /**
     * @ORM\ManyToOne(targetEntity="pDev\PracticasBundle\Entity\Practica", inversedBy="practicantes")
     * @ORM\JoinColumn(name="practica_id", referencedColumnName="id", nullable=true)
     */
    private $practica;
    
    /**
     * @ORM\ManyToOne(targetEntity="pDev\PracticasBundle\Entity\OrganizacionAlias")
     * @ORM\JoinColumn(name="organizacionalias_id", referencedColumnName="id", nullable=false)
     */
    private $organizacionAlias;

    /**
     * @var string
     *
     * @ORM\Column(name="comocontacto", type="string")     
     */
    private $comoContacto;
    
    /**
     * @var string
     *
     * @ORM\Column(name="codigopractica", type="string",nullable=true)     
     */
    private $codigoPractica;
    
    /**
     * @var string
     *
     * @ORM\Column(name="ultimoTaller", type="string")     
     */
    private $ultimoTaller;
    
    /**
     * @var string
     *
     * @ORM\Column(name="ultimoTallerProfesor", type="string",nullable=true)     
     */
    private $ultimoTallerProfesor;
    
    /**
     * @var string
     *
     * @ORM\Column(name="tipo", type="string", length=255)
     */
    private $tipo;
    
    /**
     * @var string
     *
     * @ORM\Column(name="estado", type="string", length=255)
     */
    private $estado;
    
    /**
     * @var string
     *
     * @ORM\Column(name="estadoObservaciones", type="text",nullable=true)
     */
    private $estadoObservaciones;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaInicio", type="datetime")
     */
    private $fechaInicio;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaTermino", type="datetime")
     */
    private $fechaTermino;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="horasLunes", type="integer")
     */
    private $horasLunes;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="horasMartes", type="integer")
     */
    private $horasMartes;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="horasMiercoles", type="integer")
     */
    private $horasMiercoles;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="horasJueves", type="integer")
     */
    private $horasJueves;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="horasViernes", type="integer")
     */
    private $horasViernes;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="horasSabado", type="integer")
     */
    private $horasSabado;
    
    /**
     * @ORM\OneToMany(targetEntity="pDev\PracticasBundle\Entity\Proyecto", mappedBy="practicante", cascade={"persist"})
     */
    private $proyectos;
    
    /**
     * @ORM\OneToMany(targetEntity="pDev\PracticasBundle\Entity\Desafio", mappedBy="practicante", cascade={"persist"})
     */
    private $desafios;
    

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set comoContacto
     *
     * @param string $comoContacto
     * @return AlumnoPracticante
     */
    public function setComoContacto($comoContacto)
    {
        $this->comoContacto = $comoContacto;
    
        return $this;
    }

    /**
     * Get comoContacto
     *
     * @return string 
     */
    public function getComoContacto()
    {
        return $this->comoContacto;
    }
    
    /**
     * Set codigoPractica
     *
     * @param string $codigoPractica
     * @return AlumnoPracticante
     */
    public function setCodigoPractica($codigoPractica)
    {
        $this->odigoPractica = $codigoPractica;
    
        return $this;
    }

    /**
     * Get codigoPractica
     *
     * @return string 
     */
    public function getCodigoPractica()
    {
        return $this->codigoPractica;
    }

    /**
     * Set ultimoTaller
     *
     * @param string $ultimoTaller
     * @return AlumnoPracticante
     */
    public function setUltimoTaller($ultimoTaller)
    {
        $this->ultimoTaller = $ultimoTaller;
    
        return $this;
    }

    /**
     * Get ultimoTaller
     *
     * @return string 
     */
    public function getUltimoTaller()
    {
        return $this->ultimoTaller;
    }

    /**
     * Set ultimoTallerProfesor
     *
     * @param string $ultimoTallerProfesor
     * @return AlumnoPracticante
     */
    public function setUltimoTallerProfesor($ultimoTaller)
    {
        $this->ultimoTallerProfesor = $ultimoTaller;
    
        return $this;
    }

    /**
     * Get ultimoTallerProfesor
     *
     * @return string 
     */
    public function getUltimoTallerProfesor()
    {
        return $this->ultimoTallerProfesor;
    }
    
    /**
     * Set tipo
     *
     * @param string $tipo
     * @return Practica
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    
        return $this;
    }

    /**
     * Get tipo
     *
     * @return string 
     */
    public function getTipo()
    {
        return $this->tipo;
    }
    
    /**
     * Set estado
     *
     * @param string $estado
     * @return Practica
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;
    
        return $this;
    }

    /**
     * Get estado
     *
     * @return string 
     */
    public function getEstado()
    {
        return $this->estado;
    }
    
    /**
     * Set estadoObservaciones
     *
     * @param string $estadoObservaciones
     * @return AlumnoPracticante
     */
    public function setEstadoObservaciones($estadoObservaciones)
    {
        $this->estadoObservaciones = $estadoObservaciones;
    
        return $this;
    }

    /**
     * Get estadoObservaciones
     *
     * @return string 
     */
    public function getEstadoObservaciones()
    {
        return $this->estadoObservaciones;
    }
    
    /**
     * Set fechaInicio
     *
     * @param \DateTime $fechaInicio
     * @return AlumnoPracticante
     */
    public function setFechaInicio($fechaInicio)
    {
        $this->fechaInicio = $fechaInicio;
    
        return $this;
    }

    /**
     * Get fechaInicio
     *
     * @return \DateTime 
     */
    public function getFechaInicio()
    {
        return $this->fechaInicio;
    }

    /**
     * Set fechaTermino
     *
     * @param \DateTime $fechaTermino
     * @return AlumnoPracticante
     */
    public function setFechaTermino($fechaTermino)
    {
        $this->fechaTermino = $fechaTermino;
    
        return $this;
    }

    /**
     * Get fechaTermino
     *
     * @return \DateTime 
     */
    public function getFechaTermino()
    {
        return $this->fechaTermino;
    }

    /**
     * Set horasLunes
     *
     * @param integer $horasLunes
     * @return AlumnoPracticante
     */
    public function setHorasLunes($horasLunes)
    {
        $this->horasLunes = $horasLunes;
    
        return $this;
    }

    /**
     * Get horasLunes
     *
     * @return integer 
     */
    public function getHorasLunes()
    {
        return $this->horasLunes;
    }

    /**
     * Set horasMartes
     *
     * @param integer $horasMartes
     * @return AlumnoPracticante
     */
    public function setHorasMartes($horasMartes)
    {
        $this->horasMartes = $horasMartes;
    
        return $this;
    }

    /**
     * Get horasMartes
     *
     * @return integer 
     */
    public function getHorasMartes()
    {
        return $this->horasMartes;
    }

    /**
     * Set horasMiercoles
     *
     * @param integer $horasMiercoles
     * @return AlumnoPracticante
     */
    public function setHorasMiercoles($horasMiercoles)
    {
        $this->horasMiercoles = $horasMiercoles;
    
        return $this;
    }

    /**
     * Get horasMiercoles
     *
     * @return integer 
     */
    public function getHorasMiercoles()
    {
        return $this->horasMiercoles;
    }

    /**
     * Set horasJueves
     *
     * @param integer $horasJueves
     * @return AlumnoPracticante
     */
    public function setHorasJueves($horasJueves)
    {
        $this->horasJueves = $horasJueves;
    
        return $this;
    }

    /**
     * Get horasJueves
     *
     * @return integer 
     */
    public function getHorasJueves()
    {
        return $this->horasJueves;
    }

    /**
     * Set horasViernes
     *
     * @param integer $horasViernes
     * @return AlumnoPracticante
     */
    public function setHorasViernes($horasViernes)
    {
        $this->horasViernes = $horasViernes;
    
        return $this;
    }

    /**
     * Get horasViernes
     *
     * @return integer 
     */
    public function getHorasViernes()
    {
        return $this->horasViernes;
    }

    /**
     * Set horasSabado
     *
     * @param integer $horasSabado
     * @return AlumnoPracticante
     */
    public function setHorasSabado($horasSabado)
    {
        $this->horasSabado = $horasSabado;
    
        return $this;
    }

    /**
     * Get horasSabado
     *
     * @return integer 
     */
    public function getHorasSabado()
    {
        return $this->horasSabado;
    }

    /**
     * Set profesor
     *
     * @param \pDev\PracticasBundle\Entity\ProfesorEvaluador $profesor
     * @return AlumnoPracticante
     */
    public function setProfesor(\pDev\PracticasBundle\Entity\ProfesorEvaluador $profesor = null)
    {
        $this->profesor = $profesor;
    
        return $this;
    }

    /**
     * Get profesor
     *
     * @return \pDev\PracticasBundle\Entity\ProfesorEvaluador 
     */
    public function getProfesor()
    {
        return $this->profesor;
    }

    /**
     * Set supervisor
     *
     * @param \pDev\PracticasBundle\Entity\Supervisor $supervisor
     * @return AlumnoPracticante
     */
    public function setSupervisor(\pDev\PracticasBundle\Entity\Supervisor $supervisor = null)
    {
        $this->supervisor = $supervisor;
    
        return $this;
    }

    /**
     * Get supervisor
     *
     * @return \pDev\PracticasBundle\Entity\Supervisor 
     */
    public function getSupervisor()
    {
        return $this->supervisor;
    }
    
    /**
     * Has supervisor
     *
     * @param \pDev\PracticasBundle\Entity\Supervisor $supervisor
     * @return boolean
     */
    public function hasSupervisor(\pDev\PracticasBundle\Entity\Supervisor $supervisor)
    {
        return $this->supervisor === $supervisor;
    }

    /**
     * Set profesorEvaluacion
     *
     * @param \pDev\PracticasBundle\Entity\EvaluacionProfesor $profesorEvaluacion
     * @return AlumnoPracticante
     */
    public function setProfesorEvaluacion(\pDev\PracticasBundle\Entity\EvaluacionProfesor $profesorEvaluacion = null)
    {
        $this->profesorEvaluacion = $profesorEvaluacion;
    
        return $this;
    }

    /**
     * Get profesorEvaluacion
     *
     * @return \pDev\PracticasBundle\Entity\EvaluacionProfesor 
     */
    public function getProfesorEvaluacion()
    {
        return $this->profesorEvaluacion;
    }

    /**
     * Set supervisorEvaluacion
     *
     * @param \pDev\PracticasBundle\Entity\EvaluacionSupervisor $supervisorEvaluacion
     * @return AlumnoPracticante
     */
    public function setSupervisorEvaluacion(\pDev\PracticasBundle\Entity\EvaluacionSupervisor $supervisorEvaluacion = null)
    {
        $this->supervisorEvaluacion = $supervisorEvaluacion;
    
        return $this;
    }

    /**
     * Get supervisorEvaluacion
     *
     * @return \pDev\PracticasBundle\Entity\EvaluacionSupervisor 
     */
    public function getSupervisorEvaluacion()
    {
        return $this->supervisorEvaluacion;
    }

    /**
     * Set alumno
     *
     * @param \pDev\UserBundle\Entity\Alumno $alumno
     * @return AlumnoPracticante
     */
    public function setAlumno(\pDev\UserBundle\Entity\Alumno $alumno)
    {
        $this->alumno = $alumno;
    
        return $this;
    }

    /**
     * Get alumno
     *
     * @return \pDev\UserBundle\Entity\Alumno 
     */
    public function getAlumno()
    {
        return $this->alumno;
    }
    
    /**
     * Has alumno
     *
     * @param \pDev\UserBundle\Entity\Alumno $alumno
     * @return boolean
     */
    public function hasAlumno(\pDev\UserBundle\Entity\Alumno $alumno)
    {
        return $this->alumno === $alumno;
    }
    
    /**
     * Set organizacionAlias
     *
     * @param \pDev\PracticasBundle\Entity\OrganizacionAlias $organizacion
     * @return AlumnoPracticante
     */
    public function setOrganizacionAlias(\pDev\PracticasBundle\Entity\OrganizacionAlias $organizacion)
    {
        $this->organizacionAlias = $organizacion;
    
        return $this;
    }

    /**
     * Get organizacionAlias
     *
     * @return \pDev\PracticasBundle\Entity\OrganizacionAlias
     */
    public function getOrganizacionAlias()
    {
        return $this->organizacionAlias;
    }

    /**
     * Set practica
     *
     * @param \pDev\PracticasBundle\Entity\Practica $practica
     * @return AlumnoPracticante
     */
    public function setPractica(\pDev\PracticasBundle\Entity\Practica $practica)
    {
        $this->practica = $practica;
    
        return $this;
    }

    /**
     * Get practica
     *
     * @return \pDev\PracticasBundle\Entity\Practica 
     */
    public function getPractica()
    {
        return $this->practica;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->proyectos = new \Doctrine\Common\Collections\ArrayCollection();
        $this->desafios = new \Doctrine\Common\Collections\ArrayCollection();
        $this->estado = AlumnoPracticante::ESTADO_PENDIENTE;
    }
    
    /**
     * Add proyectos
     *
     * @param \pDev\PracticasBundle\Entity\Proyecto $proyectos
     * @return AlumnoPracticante
     */
    public function addProyecto(\pDev\PracticasBundle\Entity\Proyecto $proyectos)
    {
        $this->proyectos[] = $proyectos;
        $proyectos->setPracticante($this);
        
        return $this;
    }

    /**
     * Remove proyectos
     *
     * @param \pDev\PracticasBundle\Entity\Proyecto $proyectos
     */
    public function removeProyecto(\pDev\PracticasBundle\Entity\Proyecto $proyectos)
    {
        $this->proyectos->removeElement($proyectos);
    }

    /**
     * Get proyectos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProyectos()
    {
        return $this->proyectos;
    }

    /**
     * Add desafios
     *
     * @param \pDev\PracticasBundle\Entity\Desafio $desafios
     * @return AlumnoPracticante
     */
    public function addDesafio(\pDev\PracticasBundle\Entity\Desafio $desafios)
    {
        $this->desafios[] = $desafios;
        $desafios->setPracticante($this);
        
        return $this;
    }

    /**
     * Remove desafios
     *
     * @param \pDev\PracticasBundle\Entity\Desafio $desafios
     */
    public function removeDesafio(\pDev\PracticasBundle\Entity\Desafio $desafios)
    {
        $this->desafios->removeElement($desafios);
    }

    /**
     * Get desafios
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDesafios()
    {
        return $this->desafios;
    }
    
    /**
     * Has contacto
     *
     * @param \pDev\PracticasBundle\Entity\Contacto $contactoBuscado
     * @return boolean 
     */
    public function hasContacto(\pDev\PracticasBundle\Entity\Contacto $contactoBuscado)
    {
        return $this->organizacionAlias->getOrganizacion()->hasContacto($contactoBuscado);
    }
}
