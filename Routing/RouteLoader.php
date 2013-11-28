<?php 
namespace Majes\CmsBundle\Routing;
 
use Symfony\Cmf\Component\Routing\RouteProviderInterface;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route as SymfonyRoute;
use Symfony\Component\HttpFoundation\Request;
 
class RouteLoader implements RouteProviderInterface{

    private $loaded = false;
    private $em = null;
    private $_pages = array();

    public function __construct($em){

        $this->em = $em;

    }

    public function getRouteCollectionForRequest(Request $request)
    {

        $collection = new RouteCollection();

        //Get langs
        $routes = $this->em->getRepository('MajesCmsBundle:Route')
            ->findAll();

        foreach($routes as $route){

            $redirect_url = $route->getRedirectUrl();

            if(!empty($redirect_url))
            {
                $collection->add('majes_cms_'.$route->getPage()->getId().'_'.$route->getLocale(), 
                    new SymfonyRoute(
                            $route->getUrl(), 
                            array('_controller' => 'FrameworkBundle:Redirect:urlRedirect', 'path' => $redirect_url)
                        )
                );
            }else{
                $collection->add('majes_cms_'.$route->getPage()->getId().'_'.$route->getLocale(), 
                    new SymfonyRoute(
                            $route->getUrl(), 
                            array('_controller' => 'MajesCmsBundle:Index:load', '_locale' => $route->getLocale()), 
                            array('domain' => $route->getHost()), 
                            array('page_id' => $route->getPage()->getId(), 'lang' => $route->getLocale())
                        )
                );
            }

        }
        return $collection;
    }

    /**
     * {@inheritDoc}
     */
    public function getRouteByName($name, $parameters = array())
    {
 
        $explode = explode('_', $name);
        if(!isset($explode[2])) return false;

        $page = $this->em->getRepository('MajesCmsBundle:Page')
            ->findOneById($explode[2]);

        $route = $this->em->getRepository('MajesCmsBundle:Route')->findOneBy(array(
            'page' => $page,
            'locale' => $explode[3]
            ));

        if (!$route) {
            return;
        }

        $redirect_url = $route->getRedirectUrl();
        if(!empty($redirect_url))
        {
            return new SymfonyRoute(
                        $route->getUrl(), 
                        array('_controller' => 'FrameworkBundle:Redirect:urlRedirect')
                    );
        }else{

            return new SymfonyRoute(
                        $route->getUrl(), 
                        array('_controller' => 'MajesCmsBundle:Index:load'), 
                        array('domain' => $route->getHost()), 
                        array('page_id' => $page->getId(), 'lang' => $route->getLocale())
                    );
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getRoutesByNames($names, $parameters = array())
    {


        $routes = array();
        foreach ($names as $name) {
            try {
                $routes[] = $this->getRouteByName($name, $parameters);
            } catch (RouteNotFoundException $e) {
                // not found
            }
        }

        return $routes;
    }
    
    
}