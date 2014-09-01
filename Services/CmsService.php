<?php

namespace Majes\CmsBundle\Services;

use Doctrine\ORM\EntityManager;
use Majes\MediaBundle\Entity\Media;
use Majes\MediaBundle\Library\Image;

class CmsService {

    private $_em;

    public function __construct($em) {
        $this->_em = $em;
    }

    public function getContent($id, $lang)
    {

        $pageLang = $this->_em->getRepository('MajesCmsBundle:PageLang')
            ->findOneBy(array(
                'page' => $id,
                'locale' => $lang));

        $page = $pageLang->getPage();

        $content = $this->_em->getRepository('MajesCmsBundle:Page')
                    ->getContent($page, $lang);

        return array(
            'content' => $content,
            'page' => $page,
            'host' => $page->getHost()->getId(),
            'template' => $page->getTemplate()->getRef()
            );
    }

    public function getMenu($host_id, $lang, $ref, $level, $page_id, $is_inmenu, $page_parent_id, $is_active){
        
        $menu = $this->_em->getRepository('MajesCmsBundle:Page')
                    ->getMenu($host_id, $lang, $ref, $level, $page_id, $is_inmenu, '', $page_parent_id, $is_active);

        return $menu;
    
    }

}
