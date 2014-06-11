<?php 
namespace Majes\CmsBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Majes\CmsBundle\Entity\Route;

class PageRepository extends EntityRepository
{

    private $_menu;
    private $_pages;

    /**
     * GET all pages for a specific host and then generate menu
     */
    public function getMenu($host_id, $lang, $menu = 'main', $level = null, $current_page_id = null, $is_inmenu = null, $status = '', $page_parent_id = null) {

        $query = $this->createQueryBuilder('p')
            ->innerJoin('p.langs', 'pl')
            ->innerJoin('p.menu', 'm')
            ->where('p.status = :status AND p.host = :host_id AND pl.locale = :lang AND m.ref = :menu');

        if(!is_null($is_inmenu)){

            $query = $query->andWhere('p.isInmenu = :is_inmenu')
                ->setParameter('is_inmenu', $is_inmenu);

        }

        if(!is_null($page_parent_id)){

            $query = $query->andWhere('p.parent = :page_parent_id')
                ->setParameter('page_parent_id', $page_parent_id);

        }

        $query = $query->setParameter('host_id', $host_id)
            ->setParameter('lang', $lang)
            ->setParameter('menu', $menu)
            ->setParameter('status', $status)
            ->orderBy('p.sort', 'ASC')
            ->getQuery();

        $results = $query->getResult();
        $this->_menu = array();


        $i = 0; $array = array();
        foreach ($results as $result) {
            $lang_page = $result->setLang($lang);
            $lang_page = $result->getLang();
            
            if(!is_null($page_parent_id)) $result->setParent(null);

            $array[] = $result;
            $i++;
        }

        return $this->cleanNavArray($this->generateNav($array, $lang, null, $level, $current_page_id));
    }

    private function generateNav($pages, $lang, $idparent = null, $maxlevel = null, $current_page_id = null) {

        foreach ($pages as $key => $page) {
            $pageLangs = $page->setLang($lang);
            $pageLang = $page->getLang();
            $parent_id = !is_null($page->getParent()) ? $page->getParent()->getId() : null;

            $page_id = (int) ($page->getId());
            if ($parent_id == $idparent) {

                //$this->_menu[$page_id]['row'] = $page;

                if (isset($this->_menu[$parent_id])) {
                    $level = $this->_menu[$parent_id]['level'] + 1;
                } else {
                    $level = 1;
                }

                if (is_null($maxlevel) || $level <= $maxlevel) {

                    $this->_menu[$page_id]['label'] = $pageLang->getTitle();
                    $this->_menu[$page_id]['id'] = $page_id;
                    $this->_menu[$page_id]['level'] = $level;
                    $this->_menu[$page_id]['url_alias'] = $pageLang->getUrl() == '/' ? '/' : '/'.$pageLang->getUrl();
                    $this->_menu[$page_id]['title'] = $pageLang->getTitle();
                    $this->_menu[$page_id]['link_url'] = $page->getLinkUrl();
                    $this->_menu[$page_id]['target_url'] = $page->getTargetUrl();
                    $this->_menu[$page_id]['is_inmenu'] = $page->getIsinmenu();
                    $this->_menu[$page_id]['is_active'] = $page->getIsActive();
                    $this->_menu[$page_id]['is_folder'] = $page->getIsFolder();
                    $this->_menu[$page_id]['sort'] = $page->getSort();
                    $this->_menu[$page_id]['parent_id'] = is_null($parent_id) ? 0 : $parent_id;

                    $current = ($page_id == $current_page_id) ? true : false;

                    $this->_menu[$page_id]['current'] =  $current;
                    if($current && $this->_menu[$page_id]['parent_id']){
                        $this->setCurrent($parent_id);
                    }
                    
                    unset($pages[$key]);

                    $this->generateNav($pages, $lang, $page_id, $maxlevel, $current_page_id);
                }else
                    unset($this->_menu[$page_id]);
            }
        }
        return $this->_menu;
    }


    public function cleanNavArray($nav, $returnNav = null, $parent_id = null, $page_id = null) {

        if ($returnNav == null)
            $returnNav = array();

        $return = false;
        if (is_array($nav))
            foreach ($nav as $key => $value) {
                if ($value['parent_id'] == $parent_id && $key != $page_id) {

                    if ($page_id == $value['parent_id']) {

                        $returnNav[$key] = $value;
                        $returnNav[$key]['children'] = array();

                        $children = $this->cleanNavArray($nav, $returnNav[$key]['children'], $value['parent_id'], $key);
                        $returnNav[$key]['children'] = !is_null($children) ? array_values($children) : null;
                        $return = true;
                    }
                } elseif ($value['parent_id'] == $page_id) {

                    $returnNav[$key] = $value;
                    $returnNav[$key]['children'] = array();

                    $children = $this->cleanNavArray($nav, $returnNav[$key]['children'], $value['parent_id'], $key);
                    $returnNav[$key]['children'] = !is_null($children) ? array_values($children) : null;

                    $return = true;
                }
            }

        if ($return)
            return $returnNav;
        else
            return null;
    }

    protected function setCurrent($page_id){

        $this->_menu[$page_id]['current'] = true;
        if($this->_menu[$page_id]['parent_id']) $this->setCurrent($this->_menu[$page_id]['parent_id']);

    }


    /**
     * Set a page template blocks
     */
    public function getBlocks($page, $lang){

        $pageTemplateBlockRepo = $this->getEntityManager()->getRepository('MajesCmsBundle:PageTemplateBlock');

        $template = $page->getTemplate();
        $template_blocks = $template->getTemplateBlocks();

        $response = array();

        //Foreach block of a template
        foreach ($template_blocks as $template_block) {
            
            //Check if there is content for this page on this specific block
            $pageTemplateBlock = $pageTemplateBlockRepo->findBy(
                array('templateBlock' => $template_block, 'page' => $page, 'locale' => $lang));



            $content = false; $page_template_block_id = null; $has_draft = false;
            $updateDate = new \DateTime();
            if(isset($pageTemplateBlock[0])){

                $draft = $pageTemplateBlock[0]->getDraft();
                $has_draft = is_null($draft) ? false : true;

                if($has_draft){
                    $content = $draft->getContent();
                    $updateDate = $draft->getUpdateDate();
                }
                else{
                    $content = $pageTemplateBlock[0]->getContent();
                    $updateDate = $pageTemplateBlock[0]->getUpdateDate();
                }

                $content = json_decode($content, true);
                $page_template_block_id = $pageTemplateBlock[0]->getId();
            }

            //Get params of this block
            $block = $template_block->getBlock();
            $title = $template_block->getTitle();
            $title = !empty($title) ? $template_block->getTitle() : $block->getTitle();

            $response[$template_block->getId()] = array(
                'block' => $block->getTitle(),
                'title' => $title,
                'page' => $page->getId(),
                'template_block' => $template_block->getId(),
                'page_template_block' => $page_template_block_id,
                'is_repeatable' => $template_block->getIsRepeatable(),
                'is_mobile' => $template_block->getIsMobile(),
                'is_tablet' => $template_block->getIsTablet(),
                'is_desktop' => $template_block->getIsDesktop(),
                'sort' => $template_block->getIsMobile(),
                'has_draft' => $has_draft,
                'update_date' => $updateDate,
                'items' => array()
                );

            $block_attributes = $block->getBlockAttributes();
            $is_repeatable = $template_block->getIsRepeatable();
        
            //If there is a content, then populate the attribute array
            if($content){ 
                $order = 0;
                foreach($content['attributes'] as $key => $attributes){

                    //If block is repeatable, then get the real index, otherwise we only need index 0 (back front end purpose)
                    if($is_repeatable) $index = $key;
                    else $index = 0;

                    //Set id and title of the block, title is only used for repeatable block
                    $response[$template_block->getId()]['items'][$index]['title'] = $attributes['title'];
                    $response[$template_block->getId()]['items'][$index]['id'] = $attributes['id'];
                    $response[$template_block->getId()]['items'][$index]['order'] = $order;

                    //Parse all attributes of a set
                    foreach($block_attributes as $block_attribute){
                        $attribute = $block_attribute->getAttribute();
                        $title = $block_attribute->getTitle();
                        $title = empty($title) ? $attribute->getTitle() : $title;
                        
                        $response[$template_block->getId()]['items'][$index]['attributes'][] = array(
                            'title' => $title,
                            'ref' => $attribute->getRef(),
                            'block_attribute_ref' => $block_attribute->getRef(),
                            'block_attribute_id' => $block_attribute->getId(),
                            'value' => isset($content['attributes'][$attributes['id']]['content'][$block_attribute->getRef()]) ? $content['attributes'][$attributes['id']]['content'][$block_attribute->getRef()] : false
                        );
                    }

                    $order++;
                        
                }

                //If is repeatable, then we set an empty item
                if($is_repeatable){
                    $response[$template_block->getId()]['items'][$index+1]['title'] = $is_repeatable ? 'New item' : '';
                    $response[$template_block->getId()]['items'][$index+1]['id'] = '';
                    $response[$template_block->getId()]['items'][$index+1]['new'] = true;
    
                    foreach($block_attributes as $block_attribute){
                        $attribute = $block_attribute->getAttribute();
                        $title = $block_attribute->getTitle();
                        $title = empty($title) ? $attribute->getTitle() : $title;
                        
                        $response[$template_block->getId()]['items'][$index+1]['attributes'][] = array(
                            'title' => $title,
                            'ref' => $attribute->getRef(),
                            'block_attribute_ref' => $block_attribute->getRef(),
                            'block_attribute_id' => $block_attribute->getId(),
                            'value' => false
                        );
                    }
                }

            }else{

                //If there is no content yet, then we set an empty one
                $response[$template_block->getId()]['items'][0]['title'] = $is_repeatable ? 'New item' : '';
                $response[$template_block->getId()]['items'][0]['id'] = '';
                if($is_repeatable) $response[$template_block->getId()]['items'][0]['new'] = true;
                foreach($block_attributes as $block_attribute){
                    $attribute = $block_attribute->getAttribute();
                    $title = $block_attribute->getTitle();
                    $title = empty($title) ? $attribute->getTitle() : $title;
                    
                    $response[$template_block->getId()]['items'][0]['attributes'][] = array(
                        'title' => $title,
                        'ref' => $attribute->getRef(),
                        'block_attribute_ref' => $block_attribute->getRef(),
                        'block_attribute_id' => $block_attribute->getId(),
                        'value' => false
                    );
                }
                
            }


        }
        return $response;
    }

    /**
     * Set a page template blocks
     */
    public function getBlock($page, $template_block, $lang, $id){

        //Check if there is content for this page on this specific block
        $pageTemplateBlockRepo = $this->getEntityManager()->getRepository('MajesCmsBundle:PageTemplateBlock');
        $response = array();

        
        $pageTemplateBlock = $pageTemplateBlockRepo->findBy(
            array('templateBlock' => $template_block, 'page' => $page, 'locale' => $lang));

        $content = false; $page_template_block_id = null; $has_draft = false;

        //Get draft content if exists
        if(isset($pageTemplateBlock[0])){
            $draft = $pageTemplateBlock[0]->getDraft();
            $has_draft = is_null($draft) ? false : true;

            $page_template_block_id = $pageTemplateBlock[0]->getId();

            if($has_draft) $pageTemplateBlock[0] = $draft;

            $content = $pageTemplateBlock[0]->getContent();
            $content = json_decode($content, true);
        }

        //Get params of this block
        $block = $template_block->getBlock();
        $title = $template_block->getTitle();
        $title = !empty($title) ? $template_block->getTitle() : $block->getTitle();

        $response = array(
            'block' => $block->getTitle(),
            'title' => $title,
            'page' => $page->getId(),
            'template_block' => $template_block->getId(),
            'page_template_block' => $page_template_block_id,
            'is_repeatable' => $template_block->getIsRepeatable(),
            'is_mobile' => $template_block->getIsMobile(),
            'is_tablet' => $template_block->getIsTablet(),
            'is_desktop' => $template_block->getIsDesktop(),
            'sort' => $template_block->getIsMobile(),
            'has_draft' => $has_draft,
            'item' => array()
            );

        $block_attributes = $block->getBlockAttributes();

        //If there is a content, then populate the attribute array
        if($content && !empty($id)){

            foreach($content['attributes'] as $key => $attributes){

                if($attributes['id'] == $id){   

                    $response['item']['title'] = $attributes['title'];
                    $response['item']['id'] = $attributes['id'];

                    foreach($block_attributes as $block_attribute){
                        $attribute = $block_attribute->getAttribute();
                        $title = $block_attribute->getTitle();
                        $title = empty($title) ? $attribute->getTitle() : $title;

                        $response['item']['attributes'][] = array(
                            'title' => $title,
                            'ref' => $attribute->getRef(),
                            'block_attribute_ref' => $block_attribute->getRef(),
                            'block_attribute_id' => $block_attribute->getId(),
                            'value' => isset($content['attributes'][$key]['content'][$block_attribute->getRef()]) ? $content['attributes'][$key]['content'][$block_attribute->getRef()] : false
                        );
                    }
                }
            }
        }else{
            
            $response['item']['title'] = '';
            $response['item']['id'] = '';

            foreach($block_attributes as $block_attribute){
                $attribute = $block_attribute->getAttribute();
                $title = $block_attribute->getTitle();
                $title = empty($title) ? $attribute->getTitle() : $title; 

                $response['item']['attributes'][] = array(
                    'title' => $title,
                    'ref' => $attribute->getRef(),
                    'block_attribute_ref' => $block_attribute->getRef(),
                    'block_attribute_id' => $block_attribute->getId(),
                    'value' => false
                );
            }
            
        }
        return $response;
    }


    /**
     * Get page content
     */
    public function getContent($page, $lang, $isDraft = false){

        $pageTemplateBlockRepo = $this->getEntityManager()->getRepository('MajesCmsBundle:PageTemplateBlock');

        $template = $page->getTemplate();
        $template_blocks = $template->getTemplateBlocks();

        $response = array();

        //Foreach block of a template
        foreach ($template_blocks as $template_block) {
            
            //Check if there is content for this page on this specific block
            $pageTemplateBlock = $pageTemplateBlockRepo->findBy(
                array('templateBlock' => $template_block, 'page' => $page, 'locale' => $lang));


            $content = false; $page_template_block_id = null; $has_draft = false;
            if(isset($pageTemplateBlock[0])){

                $draft = $pageTemplateBlock[0]->getDraft();
                $has_draft = is_null($draft) ? false : true;

                if($has_draft && $isDraft) $content = $draft->getContent();
                else $content = $pageTemplateBlock[0]->getContent();

                $content = json_decode($content, true);
                $page_template_block_id = $pageTemplateBlock[0]->getId();
            }

            //Get params of this block
            $block = $template_block->getBlock();
            
            $response[$template_block->getRef()] = array(
                'block' => $block->getTitle(),
                'block_ref' => $block->getRef(),
                'page' => $page->getId(),
                'template_block' => $template_block->getId(),
                'page_template_block' => $page_template_block_id,
                'is_repeatable' => $template_block->getIsRepeatable(),
                'is_mobile' => $template_block->getIsMobile(),
                'is_tablet' => $template_block->getIsTablet(),
                'is_desktop' => $template_block->getIsDesktop(),
                'sort' => $template_block->getSort(),
                'has_draft' => $has_draft,
                'items' => array()
                );

            $block_attributes = $block->getBlockAttributes();
            $is_repeatable = $template_block->getIsRepeatable();
        
            //If there is a content, then populate the attribute array
            if($content){ 
                $order = 0;
                foreach($content['attributes'] as $key => $attributes){

                    //If block is repeatable, then get the real index, otherwise we only need index 0 (back front end purpose)
                    if($is_repeatable) $index = $key;
                    else $index = 0;

                    //Set id and title of the block, title is only used for repeatable block
                    $response[$template_block->getRef()]['items'][$index]['title'] = $attributes['title'];
                    $response[$template_block->getRef()]['items'][$index]['id'] = $attributes['id'];
                    $response[$template_block->getRef()]['items'][$index]['order'] = $order;

                    //Parse all attributes of a set
                    foreach($block_attributes as $block_attribute){
                        $attribute = $block_attribute->getAttribute();
                        $title = $block_attribute->getTitle();
                        $title = empty($title) ? $attribute->getTitle() : $title;
                        
                        $response[$template_block->getRef()]['items'][$index]['attributes'][$block_attribute->getRef()] = array(
                            'title' => $title,
                            'ref' => $attribute->getRef(),
                            'block_attribute_ref' => $block_attribute->getRef(),
                            'block_attribute_id' => $block_attribute->getId(),
                            'value' => isset($content['attributes'][$attributes['id']]['content'][$block_attribute->getRef()]) ? $content['attributes'][$attributes['id']]['content'][$block_attribute->getRef()] : false
                        );
                    }
                    $order++;
                }


            }


        }
        return $response;
    }


    /**
     * Generate ROUTE
     */
    public function generateRoutes($menu_ref = 'main', $is_multilingual = true){

        $em = $this->getEntityManager();
        //Get langs
        $langs = $em->getRepository('MajesCoreBundle:Language')
            ->findBy(array('isActive' => 1));

        //Get hosts
        $hosts = $em->getRepository('MajesCoreBundle:Host')
            ->findAll();

        $menu = array(); $domain_langs = array();
        foreach($langs as $lang){
            
            $domain_langs[$lang->getLocale()] = $lang->getHost();

            foreach($hosts as $host){
    
                $response = $em->getRepository('MajesCmsBundle:Page')
                    ->getMenu($host->getId(), $lang->getLocale(), $menu_ref);

                $response = array_values( (array)$response );
                $menu[$lang->getLocale()][$host->getUrl()] = $response;
    
            }
        }

        $pages = array();
        foreach($menu as $lang => $host){

            foreach($host as $domain => $pages){

                foreach($pages as $page){

                    $this->_pages[] = array(
                        'lang' => $lang,
                        'domain' => $domain,
                        'title' => $page['label'],
                        'url' => $page['url_alias'],
                        'link_url' => $page['link_url'],
                        'target_url' => $page['target_url'],
                        'is_folder' => $page['is_folder'],
                        'id' => $page['id']
                        );

                    $this->recursiveMenu($page['children'], $lang, $domain, $page['url_alias']);
                }
            }

        }
        foreach($this->_pages as $route){

            $domain_lang = $domain_langs[$route['lang']];

            $page = $em->getRepository('MajesCmsBundle:Page')->findOneById($route['id']);

            $routeObject = $em->getRepository('MajesCmsBundle:Route')
                        ->findOneBy(array('page' => $page, 'locale' => $route['lang'], 'host' => !empty($domain_lang) ? $domain_lang : $route['domain']));

            if(is_null($routeObject)) $routeObject = new Route();

            $route['url'] = $route['url'] == '/' ? '' : $route['url'];

            $routeObject->setLocale($route['lang']);

            if(!empty($domain_lang))
                $routeObject->setUrl($route['url']);
            elseif($is_multilingual)
                $routeObject->setUrl('/'.$route['lang'].$route['url']);
            else
                $routeObject->setUrl($route['url']);

            $routeObject->setPage($page);
            $routeObject->setHost(!empty($domain_lang) ? $domain_lang : $route['domain']);
            $routeObject->setTitle($route['title']);
            $routeObject->setRedirectUrl($route['link_url']);

            $em->persist($routeObject);
            $em->flush();
        }

    }

    public function recursiveMenu($children, $lang, $domain, $root_url){

        if(!is_array($children)) return;

        foreach($children as $page){

            $this->_pages[] = array(
                        'lang' => $lang,
                        'domain' => $domain,
                        'title' => $page['label'],
                        'url' => $root_url.$page['url_alias'],
                        'link_url' => $page['link_url'],
                        'target_url' => $page['target_url'],
                        'is_folder' => $page['is_folder'],
                        'id' => $page['id']
                        );

            $this->recursiveMenu($page['children'], $lang, $domain, $root_url.$page['url_alias']);

        }


    }

}