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
            new \Twig_SimpleFunction('getBreadcrumb', array($this, 'getBreadcrumb')),
            new \Twig_SimpleFunction('getHost', array($this, 'getHost'))
        );
    }

    public function hasTranslation($page_id, $lang){
    	
    	$page = $this->_em->getRepository('MajesCmsBundle:Page')
            ->findOneById($page_id);

        $pageLangs = $page->getLangs();
        foreach($pageLangs as $pageLang){

        	if($pageLang->getLocale() == $lang) return true;

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

    public function getMenu($host_id, $lang, $ref, $level, $page_id, $is_inmenu = 1, $page_parent_id = null){
        
        $menu = $this->_em->getRepository('MajesCmsBundle:Page')
                    ->getMenu($host_id, $lang, $ref, $level, $page_id, $is_inmenu, '', $page_parent_id);

        return $menu;
    
    }

    public function getBreadcrumb($menu){
        
        return Helper::extractBreadcrumb($menu);
    
    }

    public function getHost(){

        $domain = $_SERVER['HTTP_HOST'];
        $host = $this->_em->getRepository('MajesCmsBundle:Host')
                    ->findOneBy(array('url' => $domain));
        return $host;
    }



    public function getName()
    {
        return 'majescms_extension';
    }
}