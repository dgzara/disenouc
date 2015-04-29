<?php

namespace pDev\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Comentario
 *
 * @ORM\Table(name="nb_user_pregunta_frecuente")
 * @ORM\Entity
 */
class PreguntaFrecuente
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
     * @var integer
     *
     * @ORM\Column(name="orden", type="integer")
     */
    private $orden;
    
    /**
     * @var string
     *
     * @ORM\Column(name="pregunta", type="string")
     */
    private $pregunta;
    
    /**
     * @var string
     *
     * @ORM\Column(name="texto", type="text")
     */
    private $texto;
    

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
     * Set orden
     *
     * @param integer $orden
     * @return PreguntaFrecuente
     */
    public function setOrden($orden)
    {
        $this->orden = $orden;
    
        return $this;
    }

    /**
     * Get orden
     *
     * @return integer 
     */
    public function getOrden()
    {
        return $this->orden;
    }

    /**
     * Set pregunta
     *
     * @param string $pregunta
     * @return PreguntaFrecuente
     */
    public function setPregunta($pregunta)
    {
        $this->pregunta = $pregunta;
    
        return $this;
    }

    /**
     * Get pregunta
     *
     * @return string 
     */
    public function getPregunta()
    {
        return $this->pregunta;
    }

    /**
     * Set texto
     *
     * @param string $texto
     * @return PreguntaFrecuente
     */
    public function setTexto($texto)
    {
        $this->texto = $texto;
    
        return $this;
    }

    /**
     * Get texto
     *
     * @return string 
     */
    public function getTexto()
    {
        return $this->texto;
    }
}