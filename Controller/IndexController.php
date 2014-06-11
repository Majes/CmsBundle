<?php
namespace Majes\CmsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\HttpFoundation\Response;

use Majes\CoreBundle\Controller\SystemController;
use Majes\MediaBundle\Entity\Media;
use Majes\CmsBundle\Entity\Template;
use Majes\CmsBundle\Entity\Block;
use Majes\CmsBundle\Entity\Attribute;
use Majes\CmsBundle\Entity\BlockAttribute;
use Majes\CmsBundle\Entity\TemplateBlock;
use Majes\CmsBundle\Entity\Page;
use Majes\CmsBundle\Entity\PageLang;
use Majes\CmsBundle\Entity\PageTemplateBlock;
use Majes\CmsBundle\Entity\PageTemplateBlockVersion;

use Majes\CmsBundle\Utils\Datatype;
use Majes\CmsBundle\Utils\Helper;


class IndexController extends Controller implements SystemController
{
	
    /**
     * Load cms front page
     */
    public function loadAction(){

        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();

        //Get router and collection
        $router = $this->container->get('router');
        $collection = $router->getRouteCollection();

        //Get current route with options
        $page_id = $request->get('routeDocument')->getOption('page_id');
        $lang = $request->get('routeDocument')->getOption('lang');
        $path = $request->get('routeDocument')->getPath();

        $draft = is_null($request->get('draft')) ? false : true;

        $page = $em->getRepository('MajesCmsBundle:Page')
            ->findOneById($page_id);
        $page->setLang($lang);

        $pageLang = $page->getLang();
        $template = $page->getTemplate();
        $host = $page->getHost();

    
        $content = $em->getRepository('MajesCmsBundle:Page')
                    ->getContent($page, $lang, $draft);

        if($this->get('templating')->exists('MajesTeelBundle:Cms:templates/'.$template->getRef().'.html.twig'))
            $template_twig = 'MajesTeelBundle:Cms:templates/'.$template->getRef().'.html.twig';
        elseif($this->get('templating')->exists('MajesCmsBundle:Index:templates/'.$template->getRef().'.html.twig'))
            $template_twig = 'MajesCmsBundle:Index:templates/'.$template->getRef().'.html.twig';
        else
            $template_twig = 'MajesCmsBundle:Index:load.html.twig';


        //Check if user has right to edit pages
        $wysiwyg = false;
        $session = $this->container->get('session');
        $securityContext = $this->container->get('security.context');

        if($session->get('wysiwyg') && $securityContext->isGranted(array('ROLE_CMS_PUBLISH', 'ROLE_SUPERADMIN')) ){
            // authenticated REMEMBERED, FULLY will imply REMEMBERED (NON anonymous)
            $wysiwyg = true;
        }

        return $this->render($template_twig, array(
            'page' => $page,
            'pageLang' => $pageLang,
            'lang' => $lang,
            'content' => $content,
            'draft' => $draft,
            'wysiwyg' => $wysiwyg,
            'template' => ($template->getRef()) ? $template->getRef() : ''
            ));

    }


    public function blockAction(){
        $request = $this->getRequest();

        $params = $request->get('params');
        $params = !empty($params) ? $params : array();
        return $this->render('MajesCmsBundle:Index:parts/block.html.twig', array(
            'block' => $request->get('block'),
            'params' => $params,
            'lang' => $this->_lang
            ));
    }

    public function menuAction(){
        $request = $this->getRequest();

        return $this->render('MajesCmsBundle:Index:parts/menu.html.twig', array(
            'block' => $request->get('block'),
            'lang' => $this->_lang
            ));
    }

    
}
