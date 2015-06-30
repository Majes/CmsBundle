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
                $collection->add('majes_route_'.$route->getId(), 
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
                            array(), 
                            array('page_id' => $route->getPage()->getId(), 'lang' => $route->getLocale()),
                            $route->getHost()
                        )
                );
                $collection->add('majes_route_'.$route->getId(), 
                    new SymfonyRoute(
                            $route->getUrl(), 
                            array('_controller' => 'MajesCmsBundle:Index:load', '_locale' => $route->getLocale()), 
                            array(), 
                            array('page_id' => $route->getPage()->getId(), 'lang' => $route->getLocale()),
                            $route->getHost()
                        )
                );
            }

        }

        $redirects = $this->em->getRepository('MajesCmsBundle:Redirect')
            ->findAll();

        foreach($redirects as $redirect){

            $collection->add('majes_redirect_'.$redirect->getId(), 
                new SymfonyRoute(
                        $redirect->getUrl(), 
                        array('_controller' => 'FrameworkBundle:Redirect:urlRedirect', 'path' => $redirect->getRedirectUrl(), 'permanent' => $redirect->getPermanent() )
                    )
            );

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

        if(isset($explode[3])){
            $page = $this->em->getRepository('MajesCmsBundle:Page')
                ->findOneById($explode[2]);

            $route = $this->em->getRepository('MajesCmsBundle:Route')->findOneBy(array(
                'page' => $page,
                'locale' => $explode[3]
                ));
        }else{
            if(is_numeric($explode[2])){
                $route = $this->em->getRepository('MajesCmsBundle:Route')->findOneBy(array('id' => $explode[2]));
                $page = $route->getPage();
            }else{
                $route = $this->em->getRepository('MajesCmsBundle:Route')->findOneBy(array('url' => $explode[2]));
                $page = $route->getPage();
            }
        }

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
                        array(), 
                        array('page_id' => $page->getId(), 'lang' => $route->getLocale()),
                        $route->getHost()
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
