<?php
namespace pDev\UserBundle\DependencyInjection;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use pDev\UserBundle\Entity\Notificacion;

class SearchHelper
{
    protected $container;
    protected $em;
    
    public function __construct(EntityManager $em, ContainerInterface $container)
    {
        $this->em = $em;
        $this->container = $container;
    }
        
    
    
    /*
     * retorna el numero total de entitdades del tipo
     */
    public function getEntitiesCount($entitystring)
    {
        $em = $this->em;
        $qb = $em->getRepository($entitystring)->createQueryBuilder('p');
        $count = $qb->select('COUNT(p)')
                ->getQuery()
                ->getSingleScalarResult();
        
        return $count;
    }
            
    /*
     * retorna arreglo de campos de busqueda mas los agregados
     */
    public function getPersonaFields($aditionalfields = array())
    {
        $fields = array('p.nombres',
                        'p.apellidoPaterno',
                        'p.apellidoMaterno',
                        'p.rut',
                        'p.email',
                        'p.emailSecundario'
            );
        
        foreach($aditionalfields as $a)
        {
            $fields[] = $a;
        }
        
        return $fields;
    }
    
    /*
     * ejecuta la consulta
     */
    public function getResultados($fields,$query,$qb,$maxresults=20,$maxwords=10)
    {
        
        $qb = $this->getConsulta($fields, $query, $qb, $maxwords);
        
        $results = $this->limitaResultados($qb, $maxresults);
            
        return $results;
    }
    
    /*
     * ejecuta la consulta
     */
    public function limitaResultados($qb,$maxresults=20)
    {
        $results = $qb->getQuery()
                      ->getResult();
        
        $resultscount = count($results);
        
        if($resultscount>$maxresults)
        {
            $results = $qb->setMaxResults( $maxresults )
                    ->getQuery()
                    ->getResult();
            $nm = $this->container->get("notification.manager");
            $pm = $this->container->get("permission.manager");
            $nm->createNotificacion('Límite de resultados', 'Se muestran solo '.$maxresults.' de '.$resultscount.' resultados, por favor refine su búsqueda',Notificacion::USER_ALERT);
        }
            
        return $results;
    }
    
    /*
     * ejecuta la consulta
     */
    public function getConsulta($fields,$query,$qb,$maxwords=10)
    {
        $nm = $this->container->get("notification.manager");
        $pm = $this->container->get("permission.manager");

        $words = explode(' ', $query);

        if(count($words)>$maxwords)
        {
            $words = array_slice($words,0,$maxwords);            
            $nm->createNotificacion('Máximo alcanzado', 'La consulta se limita a las primeras '.$maxwords.' palabras.',  Notificacion::USER_ALERT);
        }
        
        $keywords = array();
        foreach($words as $word)
        {
            $keywords[] = '%'.$word.'%';        
        }        
        
        for($i=0;$i<count($keywords);$i++)
        {
            $w = '';
            $first = true;
            foreach($fields as $field)
            {
                if(!$first)
                    $w .= ' OR ';
                $w .= $field.' like :ks'.$i; 
                $first = false;
            }
            
            if($i==0)
                $qb = $qb->where($w);
            else
                $qb = $qb->andWhere($w);
            
            $qb = $qb->setParameter('ks'.$i,$keywords[$i]);
        }
        
        
            
        return $qb;
    }
}
