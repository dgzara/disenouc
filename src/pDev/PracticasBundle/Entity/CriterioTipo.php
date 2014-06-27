<?php

namespace pDev\PracticasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CriterioTipo
 *
 * @ORM\Table(name="nb_practicas_criterio_tipo")
 * @ORM\Entity
 */
class CriterioTipo
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="aspecto", type="string", length=255)
     */
    private $aspecto;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="text")
     */
    private $descripcion;

    /**
     * @var string
     *
     * @ORM\Column(name="explicacion", type="text")
     */
    private $explicacion;

    /**
     * @var string
     *
     * @ORM\Column(name="tipoPractica", type="string", length=255)
     */
    private $tipoPractica;

    /**
     * @var string
     *
     * @ORM\Column(name="tipoEvaluador", type="string", length=255)
     */
    private $tipoEvaluador;


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
     * Set nombre
     *
     * @param string $nombre
     * @return CriterioTipo
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    
        return $this;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set aspecto
     *
     * @param string $aspecto
     * @return CriterioTipo
     */
    public function setAspecto($aspecto)
    {
        $this->aspecto = $aspecto;
    
        return $this;
    }

    /**
     * Get aspecto
     *
     * @return string 
     */
    public function getAspecto()
    {
        return $this->aspecto;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return CriterioTipo
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    
        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string 
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set explicacion
     *
     * @param string $explicacion
     * @return CriterioTipo
     */
    public function setExplicacion($explicacion)
    {
        $this->explicacion = $explicacion;
    
        return $this;
    }

    /**
     * Get explicacion
     *
     * @return string 
     */
    public function getExplicacion()
    {
        return $this->explicacion;
    }

    /**
     * Set tipoPractica
     *
     * @param string $tipoPractica
     * @return CriterioTipo
     */
    public function setTipoPractica($tipoPractica)
    {
        $this->tipoPractica = $tipoPractica;
    
        return $this;
    }

    /**
     * Get tipoPractica
     *
     * @return string 
     */
    public function getTipoPractica()
    {
        return $this->tipoPractica;
    }

    /**
     * Set tipoEvaluador
     *
     * @param string $tipoEvaluador
     * @return CriterioTipo
     */
    public function setTipoEvaluador($tipoEvaluador)
    {
        $this->tipoEvaluador = $tipoEvaluador;
    
        return $this;
    }

    /**
     * Get tipoEvaluador
     *
     * @return string 
     */
    public function getTipoEvaluador()
    {
        return $this->tipoEvaluador;
    }
}