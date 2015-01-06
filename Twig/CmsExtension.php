<?php 
namespace Majes\CmsBundle\Twig;

use Majes\CmsBundle\Entity\Page;
use Majes\CmsBundle\Utils\Helper;

class CmsExtension extends \Twig_Extension
{
   
    private $_em;
    private $_router;
    private $_container;

    public function __construct($em, $router, $container){
        $this->_em = $em;
        $this->_router = $router;
        $this->_container = $container;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('hasTranslation', array($this, 'hasTranslation')),
            new \Twig_SimpleFunction('wysiwygTagBegin', array($this, 'wysiwygTagBegin')),
            new \Twig_SimpleFunction('wysiwygTagEnd', array($this, 'wysiwygTagEnd')),
            new \Twig_SimpleFunction('getMenu', array($this, 'getMenu')),
            new \Twig_SimpleFunction('getContent', array($this, 'getContent')),
            new \Twig_SimpleFunction('getBreadcrumb', array($this, 'getBreadcrumb')),
            new \Twig_SimpleFunction('datatypeTemplateExist', array($this, 'datatypeTemplateExist')),
            new \Twig_SimpleFunction('getHost', array($this, 'getHost'))
        );
    }

    public function hasTranslation($page_id, $lang, $admin = false){
        if(empty($page_id)) return false;
        $page = $this->_em->getRepository('MajesCmsBundle:Page')
            ->findOneById($page_id);

        if(empty($page))
            return false;

        $pageLangs = $page->getLangs();
        foreach($pageLangs as $pageLang){

            if($pageLang->getLocale() == $lang){
                if(!$admin && $pageLang->getIsActive() == false) return false;
                return true;
            }

        }

        return false;

    }

    public function wysiwygTagBegin($block, $item, $lang){
        
        
        $session = $this->_container->get('session');
        $securityContext = $this->_container->get('security.context');
        if( $session->get('wysiwyg') && $securityContext->isGranted(array('ROLE_CMS_PUBLISH', 'ROLE_SUPERADMIN')) ){
            // authenticated REMEMBERED, FULLY will imply REMEMBERED (NON anonymous)
            $url = $this->_router->generate('_cms_pageblock_form', array('lang' => $lang));
            return '<div class="wysiwyg majesTeel"><a class="editBlock glyphicon glyphicon-edit" href="'.$url.'" data-pagetemplateblock="'.$block['page_template_block'].'" data-page="'.$block['page'].'" data-templateblock="'.$block['template_block'].'" data-id="'.$item['id'].'"></a>';
        }

        return '';
        

    }

    public function wysiwygTagEnd(){
        $session = $this->_container->get('session');
        $securityContext = $this->_container->get('security.context');
        if($session->get('wysiwyg') &&  $securityContext->isGranted(array('ROLE_CMS_PUBLISH', 'ROLE_SUPERADMIN')) ){
            // authenticated REMEMBERED, FULLY will imply REMEMBERED (NON anonymous)
            return '</div>';
        }

        return '';
        

    }

    public function getMenu($host_id, $lang, $ref, $level, $page_id, $is_inmenu = 1, $page_parent_id = null, $is_active = null){
        
        $menu = $this->_container->get('majescms.cms_service')
                    ->getMenu($host_id, $lang, $ref, $level, $page_id, $is_inmenu, '', $page_parent_id, $is_active);

        return $menu;
    
    }

    public function getContent($page, $lang, $isDraft = false){
        
        if(is_int($page))
            $page = $this->_em->getRepository('MajesCmsBundle:Page')
                ->findOneById($page);

        if(empty($page)) return false;

        $content = $this->_container->get('majescms.cms_service')
                    ->getContent($page, $lang, $isDraft);

        return $content;
    
    }

    public function getBreadcrumb($menu){
        
        return Helper::extractBreadcrumb($menu);
    
    }

    public function getHost(){

        $domain = $_SERVER['HTTP_HOST'];
        $host = $this->_em->getRepository('MajesCoreBundle:Host')
                    ->findOneBy(array('url' => $domain));
        return $host;
    }

    public function datatypeTemplateExist($attributeRef){
        if(file_exists($this->_container->get('kernel')->getRootDir()."/../src/Majes/TeelBundle/Resources/views/Admin/datatype/".$attributeRef.'.html.twig'))
            return true;
        return false;
    }


    public function getName()
    {
        return 'majescms_extension';
    }
}