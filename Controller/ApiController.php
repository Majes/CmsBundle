<?php
namespace Majes\CmsBundle\Controller;

use Majes\CoreBundle\Controller\SystemController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Majes\CmsBundle\Entity\PageLang;
use Majes\CmsBundle\Entity\Page;

class ApiController extends Controller implements SystemController
{

    /**
     * @return array
     * @View()
     */
    public function getContentsAction()
    {
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();

        $_lang = $request->get('lang');

        if(empty($_lang)) $_lang = $this->_lang;

        $pages = $em->getRepository('MajesCmsBundle:Page')
            ->findAllOrdered();

        $response = array(
            'lang' => $_lang,
            'pages' => array());

        foreach($pages as $page){
            $status = $page->getStatus();
            if($status == 'deleted') continue;

            $page->setLang($_lang);
            $pageLang = $page->getLang();

            $content = $em->getRepository('MajesCmsBundle:Page')
                    ->getContent($page, $_lang);
            if(!empty($pageLang))
                $response['pages'][] = array(
                    'url' => $pageLang->getUrl(),
                    'order' => $page->getSort(),
                    'content' => $content);
            
            unset($pageLang, $content);

        }

        return $response;
    }

    /**
     * @return array
     * @View()
     */
    public function getContentAction($url)
    {

        $em = $this->getDoctrine()->getManager();
        $pageLang = $em->getRepository('MajesCmsBundle:PageLang')
            ->findOneBy(array(
                'url' => $url,
                'locale' => $this->_lang));

        $page = $pageLang->getPage();

        $content = $em->getRepository('MajesCmsBundle:Page')
                    ->getContent($page, $this->_lang);

        return array(
            'lang' => $this->_lang, 
            'pages' => array(
                0 => array(
                    'url' => $url,
                    'content' => $content
                    )
                ));
    }

}