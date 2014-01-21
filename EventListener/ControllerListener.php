<?php 
namespace Majes\CmsBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

use Majes\CoreBundle\Controller\SystemController;


class ControllerListener
{

    private $_notification;
    private $_em;

    public function __construct(EntityManager $entityManager, \Majes\CoreBundle\Services\Notification $notification)
    {
        $this->_notification = $notification;
        $this->_em = $entityManager;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();
        /*
         * $controller peut être une classe ou une closure. Ce n'est pas
         * courant dans Symfony2 mais ça peut arriver.
         * Si c'est une classe, elle est au format array
         */
        $controllerObject = $controller[0];
        if (!is_array($controller)) {
            return;
        }

        if ($controllerObject instanceof SystemController) {

            //Get last updated pages
            $pages = $this->_em->getRepository('MajesCmsBundle:PageLang')->lastUpdated();

            $notification = $this->_notification;

            $notification->set(array('_source' => 'cms'));
            $notification->reinit();

            foreach($pages as $page){
                $media_id = null;
                if(!is_null($page->getUser()->getMedia()))
                    $media_id = $page->getUser()->getMedia()->getId();

                $url = $controllerObject->get('router')->generate('_cms_content', array('id' => $page->getPage()->getId(), 'page_parent_id' => is_null($page->getPage()->getParent())? 0 : $page->getPage()->getParent()->getId(), 'menu_id' => $page->getPage()->getMenu()->getId(), 'lang' => $page->getLocale()));

                $notification->add('messages', array('status' => 'success', 'title' => '<strong>'.$page->getTitle().'</strong> has been updated', 'url' => $url, 'media_id' => $media_id));

            }
            
        }
    }
}