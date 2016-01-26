<?php
namespace Majes\CmsBundle\Controller;

use Majes\CoreBundle\Controller\SystemController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Majes\CmsBundle\Entity\PageLang;
use Majes\CmsBundle\Entity\Page;

class ApiController extends Controller implements SystemController
{

    /**
     * @return array
     * @View()
     */
    public function getContentsAction(Request $request)
    {

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

            $content = $this->container->get('majescms.cms_service')
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

        $content = $this->container->get('majescms.cms_service')
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
