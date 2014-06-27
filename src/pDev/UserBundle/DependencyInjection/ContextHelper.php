<?php
namespace pDev\UserBundle\DependencyInjection;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ContextHelper
{
    protected $container;
    protected $em;
    
    public function __construct(EntityManager $em, ContainerInterface $container)
    {
        $this->em = $em;
        $this->container = $container;
    }
    
    public function getSemestreActual()
    {
        return (new \DateTime('now'))>(new \DateTime(date("Y")."-07-15 00:00:00"))?2:1;
    }
    
    public function getYearActual()
    {
        return intval(date("Y"));
    }
    
    public function getPeriodoActual()
    {
        return array($this->getYearActual(),$this->getSemestreActual());
    }
    
    public function getPeriodoAnterior()
    {
        $sem = $this->getSemestreActual() == 2?1:2;
        $year = $sem == 2? $this->getYearActual()-1:$this->getYearActual();
        
        return array($year,$sem);
    }
    
    public function getMesActual()
    {
        $meses = array('','enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre');
        return $meses[intval(date("n"))];
    }
    
    public function getDiaActual()
    {
        return intval(date("j"));
    }
    
    public function getDiaSemanaActual()
    {
        $dias = array('','lunes','martes','miércoles','jueves','viernes','sábado','domingo');
        return $dias[intval(date("N"))];
    }
    
    
    
    public function isPeriodoAyudantias()
    {
        $em = $this->em;
        $ayudantia_inicio = $em->getRepository('pDevUserBundle:Configuracion')->findOneByKeyName('ayudantias_periodo_inicio');
        $ayudantia_termino = $em->getRepository('pDevUserBundle:Configuracion')->findOneByKeyName('ayudantias_periodo_termino');
        $now = new \DateTime();
        
        if($ayudantia_inicio->getValor()<$now and $ayudantia_termino->getValor()>$now)
            return true;
            
        return false;
    }
    
    public function isPeriodoCausales()
    {
        $em = $this->em;
        $now = new \DateTime();
        $causales_inicio = $em->getRepository('pDevUserBundle:Configuracion')->findOneByKeyName('causales_periodo_inicio');
        $causales_termino = $em->getRepository('pDevUserBundle:Configuracion')->findOneByKeyName('causales_periodo_termino');
        
        if($causales_inicio->getValor()<$now and $causales_termino->getValor()>$now)
            return true;
            
        return false;
    }
    
    public function getConfigValue($key)
    {
        $em = $this->em;        
        $config = $em->getRepository('pDevUserBundle:Configuracion')->findOneByKeyName($key);
               
        if($config)
            return $config->getValor();
        else 
            return null;
    }
    
    /**
 * ////////////////////////////////////////////////////////////////////////////////////////////////////////////
 * ////////
 * ////////    TEXT 
 * ////////
 * ////////////////////////////////////////////////////////////////////////////////////////////////////////////
*/
    
    // retorna solo letras minusculas y numeros
    public function clearText($string)
    {
        if($string)
        {
            //$string = $this->decodeText($string);
            $string = $this->asciidizeText($string);
            
	        $string = $this->trimText($string);
	        
	        $string = strtolower($string);
	        $string = preg_replace('/[^\x30-\x39\x61-\x7A]*/','', $string);
	    }
	    
        return $string;
    }
    
    //normaliza y quita los espacios al principio y al final, y el exceso de espacios
    public function trimText($string)
    {
        if($string)
        {
            $string = $this->normalizeText($string);
            $string = preg_replace('/\s\s+/', ' ', $string);
        }
        
        return trim($string);
    }
    
    //españoliza los caracteres
    public function normalizeText($string)
    {
	    if($string)
        {
            $string = $this->decodeText($string);
            
            $string = str_replace(array(' ', 'à', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'ê', 'ë', 'ð', 'ì', 'î', 'ï', 'ò', 'ô', 'õ', 'ö', 'ø', '§', 'ù', 'û', 'ý', 'ÿ', 'À', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'Ê', 'Ë', '€', 'Ð', 'Ì', 'Î', 'Ï', 'Ò', 'Ô', 'Õ', 'Ö', 'Ø', '§', 'Ù', 'Û', 'Ý', 'Ÿ'), array(' ','a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'ed', 'i', 'i', 'i', 'o', 'o', 'o', 'o', 'o', 's', 'u', 'u', 'y', 'y', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'EUR', 'ED', 'I', 'I', 'I', 'O', 'O', 'O', 'O', 'O', 'S', 'U', 'U', 'Y', 'Y'), $string);
	    
                
                
	        $string = preg_replace('/[^\x20-\x7F\xA1\xC1\xE1\xC9\xE9\xCD\xED\xD3\xF3\xDA\xFA\xD1\xDC\xF1]*/','', $string);
	        //$string = preg_replace('/[^\x20-\xFC]*/','', $string);
	        $string = $this->encodeText($string);
        }
        
        return $string;
    }
    
    public function decodeText($string)
    {
        if($string)
        {
            $order = "Windows-1252,CP1251,ASCII,UTF-8,ISO-8859-1,CP932,Windows-1251";
            $enc =mb_detect_encoding($string, $order);
            if($enc!='ISO-8859-1')
            {
                $string = mb_convert_encoding($string, "ISO-8859-1", $enc);
            }
        }
        return $string;
    }
    
    public function encodeText($string)
    {
        if($string)
        {
            $order = "Windows-1252,CP1251,ASCII,ISO-8859-1,UTF-8,CP932,Windows-1251";
            $enc =mb_detect_encoding($string, $order);
            if($enc!='UTF-8')
            {
                $string = mb_convert_encoding($string, "UTF-8", $enc);
            }
        }
        return $string;
    }
    
    //solo ascii
    public function asciidizeText($string)
    {
	    if($string)
        {
            $string = str_replace(array(' ', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ð', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', '§', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', '€', 'Ð', 'Ì', 'Í', 'Î', 'Ï', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', '§', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'Ÿ'), array(' ','a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'ed', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 's', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'EUR', 'ED', 'I', 'I', 'I', 'I', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'S', 'U', 'U', 'U', 'U', 'Y', 'Y'), $string);
	    
	        $string = preg_replace('/[^(\x20-\x7F)]*/','', $string);
        }

        return $string;
    }
    
    public function lowerizeText($string)
    {
        if($string)
        {
            $string = $this->decodeText($string);
            $string = $this->encodeText($string);
            $string = mb_strtolower($string, 'UTF-8');
        }
        return $string;
    }

    public function upperizeText($string)
    {
        if($string)
        {
            $string = $this->decodeText($string);
            $string = $this->encodeText($string);
            $string = mb_strtoupper($string, 'UTF-8');
        }
        return $string;
    }
    
    public function generateAlias($alias)
    {
        if($alias)
        {
            $alias = $this->parseNombre($alias);
            $alias = $this->asciidizeText($alias);
            $alias = $this->trimText($alias);
            $alias = $this->lowerizeText($alias);
        }
        return $alias;
    }
    
    /**
 * ////////////////////////////////////////////////////////////////////////////////////////////////////////////
 * ////////
 * ////////    PARSERS
 * ////////
 * ////////////////////////////////////////////////////////////////////////////////////////////////////////////
*/
    
    /**
     * parseRut
     *
     * @param string $rut con digito verificador
     * @param boolean $withdv retornar o no con digito verificador
     * @return $rut con digito verificador recalculado segun $withdv
     */
    public function parseRut($rut,$withdv = true)
    {
        //limpiamos el rut
        $rut = $this->upperizeText($rut);
        $rut = preg_replace("/[^0-9K]/", "", $rut);
        
        //recalculamos el digito verificador
        if(strlen($rut)>1)
        {
            $numero = strrev(substr($rut, 0, strlen($rut)-1));
            $total = strlen($numero);
            $multiplo = 2;
            $suma = 0;

            for ($i=0;$i<$total;$i++)
            {
                if ($multiplo > 7)
                    $multiplo = 2;

                $suma += $numero[$i]*$multiplo;
                $multiplo++;
            }

            $digito = (11-$suma%11)%11;

            if ($digito == 10)
                $digito = "K";
            
            $rut = strrev($numero);
            if($withdv)
                $rut .= $digito;
        }
        else
        {
            $rut = '0';
            if($withdv)
                $rut .= '0';
         }

        return $rut;

    }
    
    public function parseNumeroAlumno($nalumno,$withdv = true)
    {
        //limpiamos el rut
        $nalumno = $this->upperizeText($nalumno);
        
        $nalumno = preg_replace("/[^0-9J]/", "", $nalumno);
        return $nalumno;
        /*
         * no funciona con 2013 al menos
//recalculamos el digito verificador
        if(strlen($nalumno)>1)
        {
            $numero = strrev(substr($nalumno, 0, strlen($nalumno)-1));
            $total = strlen($numero);
            $multiplo = 2;
            $suma = 0;

            for ($i=0;$i<$total;$i++)
            {
                if ($multiplo > 7)
                    $multiplo = 2;

                $suma += $numero[$i]*$multiplo;
                $multiplo++;
            }

            $digito = (11-$suma%11)%11;

            if ($digito == 10)
                $digito = "J";
            
            $nalumno = strrev($numero);
            if($withdv)
                $nalumno .= $digito;
        }
        else
        {
            $nalumno = '0';
            if($withdv)
                $nalumno .= '0';
        }

        return $nalumno;
        */
    }
    
    //solo numeros
    public function parseNumber($string)
    {
        if($string)
        {
            $string = str_replace(',','.', $string);
	    
            $string = preg_replace('/[^(\x30-\x39\x2E)]*/','', $string);

            if(strstr($string, '.')===false)
                $string = intval($string);
            else
                $string = floatval($string);
        }

        return $string;
    }
    
    //returna el email sino null
    public function parseEmail($email, $dominios = array())
    {
        if($email)
        {
            $email = str_replace(' ','',$email);
            $email = $this->lowerizeText($email);
            $email = $this->trimText($email);
            $email_array = explode('@',$email);
            if(count($email_array)==2)
            {
                if(strstr($email_array[1], '.')===false)
                    return null;
                    
                if($dominios)
                {
                    $flag = false;
                    foreach($dominios as $dom)
                    {
                        if($email_array[1]==$dom)
                            $flag = true;
                    }
                    if(!$flag)
                        return null;
                }
            }
            else
                return null;
        }
        return $email;
    }
    
    public function parseNombre($nombre)
    {
        if($this->clearText($nombre) == 'porfijar')
            return null;
            
        $nombre = $this->lowerizeText($this->trimText($nombre));
        $nombre = str_replace('y equipo','',$nombre);
        
        $nombre = ucwords($nombre);
        
        return $nombre;
        /*
        $nombre_array = array_reverse(explode(' ',$nombre));
        $return_array = array('','','');

        $j=0;
        if(count($nombre_array)==2)
            $j=1;
        elseif(count($nombre_array)==1)
            $j=2;
        
        //apellidos alaveses
        $keys = array('de','del','la','san','von');    
        $flagLastNameClosed = false;
        for($i=0;$i<count($nombre_array);$i++)
        {
            if($i==0)
            {
                $return_array[$j] = $this->trimText($nombre_array[$i]);
                $j++;
            }
            else
            {
                $norm = $this->lowerizeText($nombre_array[$i]);
                if($j <= 2 and !$flagLastNameClosed and in_array($norm, $keys))
                {
                    $return_array[$j-1] = $this->trimText($nombre_array[$i]).' '.$return_array[$j-1];
                }
                else
                {
                    $return_array[$j] = $this->trimText($nombre_array[$i]).' '.$return_array[$j];
                    if($j < 2)
                        $j++;
                    else
                        $flagLastNameClosed = true;
                }
            }
        }
        
        $return_array[3] = $nombre;
        return $return_array;*/
    }
    
    public function parseSemestre($valor)
    {
        if($valor)
        {
            $valor = $this->clearText($valor.'');
            if($valor == 'tav')
                $valor = 3;
                
            $valor = intval($valor);
            
            if($valor == 21)
                $valor = 1;
            elseif($valor == 22)
                $valor = 2;
            elseif($valor == 23)
                $valor = 3;
        }
        return $valor;
    }
    
    public function parseCurriculum($codigo)
    {
        if($codigo)
        {
            $codigo = $this->upperizeText($codigo.'');
        }
        return $codigo;
    }
    
    public function parseSigla($codigo)
    {
        if($codigo)
        {
            $codigo = $this->upperizeText($codigo.'');
        }
        return $codigo;
    }
}
