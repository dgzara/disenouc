<?php
namespace pDev\ParserBundle\DependencyInjection;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Goutte\Client;

class CrawlerHelper
{
    protected $container;
    protected $em;
    
    public function __construct(EntityManager $em, ContainerInterface $container)
    {
        $this->em = $em;
        $this->container = $container;
    }
    
    public function getLoggedCrawler($client)
    {
        $em = $this->em;
        $maxretries = 5;
        $retries = $maxretries;

        $user = $em->getRepository('pDevUserBundle:Configuracion')->findOneByKeyName('sync_user');
        $pass = $em->getRepository('pDevUserBundle:Configuracion')->findOneByKeyName('sync_pass');

        if (!$user) {
            throw $this->createNotFoundException('Unable to find User.');
        }
        if (!$pass) {
            throw $this->createNotFoundException('Unable to find Password.');
        }
        $crawler = null;
        while($retries>0)
        {
            $crawler = $client->request('GET', 'https://www4.uc.cl/SGAD/controlador/controlador.jsp?ACCION=aplicacion&SUBACCION=index');
            
            while(302 === $client->getResponse()->getStatus())
                $crawler = $client->followRedirect();
            
            if($crawler->filter('form[name=frmAccesoPerfil]')->count()>0)
            {
                $form = $crawler->filter('form[name=frmAccesoPerfil]')->form(array(
                    'login'  => $user->getValor(),
                    'Password'  => $pass->getValor(),
                    ));      
                $crawler = $client->submit($form);
            }
            
            if(!$crawler->filter('a:contains("Alumnos")')->count()>0)
            {
                $retries--;
                sleep(3*($maxretries-$retries));
            }
            else
                break;
        }
        
        return array($crawler,($maxretries-$retries));
    }
    
    public function getLoggedCursosCrawler($client)
    {
        $em = $this->em;

        $user = $em->getRepository('pDevUserBundle:Configuracion')->findOneByKeyName('sync_user');
        $pass = $em->getRepository('pDevUserBundle:Configuracion')->findOneByKeyName('sync_pass');

        if (!$user) {
            throw $this->createNotFoundException('Unable to find User.');
        }
        if (!$pass) {
            throw $this->createNotFoundException('Unable to find Password.');
        }
        
        $client->setAuth($user->getValor(),$pass->getValor());
        
        $crawler = $client->request('GET', 'https://www2.puc.cl/ListaCurso/ListaCursoProf');
        
        
        
        if($crawler->filter('body:contains("Sigla")')->count()>0)
            return $crawler;
        else
            return null;
    }
    
    private $cachedclient = null;
    
    public function getSgadClient()
    {
        if(!$this->cachedclient)
        {
            $client = new Client();
            
            //emulamos ie10
            $client->SetHeader('ACCEPT','text/html, application/xhtml+xml, */*');
            $client->SetHeader('ACCEPT-LANGUAGE', 'es-CL,es;q=0.5');
            $client->SetHeader('USER-AGENT', 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.2; WOW64; Trident/6.0)');
            $client->SetHeader('ACCEPT-ENCODING','gzip, deflate');
            $client->SetHeader('DNT','1');
            $client->SetHeader('CONNECTION','Keep-Alive');
            
            $cachedclient = $client;
        }
        else
            $cachedclient = $this->cachedclient;
        
        return $cachedclient;
    }
    
}
