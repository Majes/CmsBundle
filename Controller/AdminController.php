<?php
namespace Majes\CmsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\HttpFoundation\Response;

use Majes\CoreBundle\Controller\SystemController;
use Majes\CoreBundle\Entity\User\Role;
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

use Majes\CoreBundle\Form\User\RoleType;
use Majes\CmsBundle\Form\BlockType;
use Majes\CmsBundle\Form\TemplateType;
use Majes\CmsBundle\Form\PageType;
use Majes\CmsBundle\Form\PageBlockType;
use Majes\CmsBundle\Form\PageRoleType;

use Majes\CoreBundle\Utils\Logger;


class AdminController extends Controller implements SystemController
{
    /**
     * @Secure(roles="ROLE_CMS_CONTENT,ROLE_SUPERADMIN")
     *
     */
    public function contentAction($id, $lang, $menu_id, $page_parent_id, $host_id)
    {   

        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();

        //If lang is null, get default language
        if(is_null($lang)) $lang = $this->_lang;
        if(is_null($page_parent_id) || $page_parent_id == 0) $page_parent_id = null;
        if(is_null($menu_id) || $menu_id == 0) $menu_id = 1;
        
        $page = $em->getRepository('MajesCmsBundle:Page')
            ->findOneById($id);

        if(is_null($host_id) && !is_null($page)) $host_id = !is_null($page->getHost()) ? $page->getHost()->getId() : null;

        //Check permissions
        if(!Helper::hasAdminRole($page, $this->container->get('security.context')))
            throw new \Exception('Unauthorized access.', 403);
       

        //Get blocks for this specific page
        $blocks = array(); $page_has_draft = false;
        if(!is_null($page)){
            $blocks = $em->getRepository('MajesCmsBundle:Page')->getBlocks($page, $lang);
            foreach($blocks as $block)
                if($block['has_draft']){
                    $page_has_draft = true;  
                    break;
                }
        }

        //Get parent
        $page_parent = $em->getRepository('MajesCmsBundle:Page')
            ->findOneById($page_parent_id);
        //Get menu
        $menu = $em->getRepository('MajesCmsBundle:Menu')
            ->findOneById($menu_id);

        //Get asked language if set
        if(!is_null($page)){
            $page->setLang($lang);
            $is_copy = $request->get('copy');
            if(is_null($page->getLang()) && !is_null($is_copy)){
                $page->setLang($this->_default_lang);
                $new_lang = clone $page->getLang();
                $new_lang->setLocale($lang);
                $new_lang->setCreateDate(new \DateTime());
                $new_lang->setUser($this->_user);

                $page->addLang($new_lang);

                

                $pageTemplateBlocks = $page->getPageTemplateBlocks();
                foreach($pageTemplateBlocks as $pageTemplateBlock){
                    $new_page_template_block = clone $pageTemplateBlock;
                    $new_page_template_block->setLocale($lang);
                    $new_page_template_block->setCreateDate(new \DateTime());
                    $new_page_template_block->setUser($this->_user);

                    $page->addPageTemplateBlock($new_page_template_block);

                }

                $em->persist($page);
                $em->flush();

                //Set routes to table
                $em->getRepository('MajesCmsBundle:Page')->generateRoutes($menu->getRef(), $this->_is_multilingual);

                return $this->redirect($this->get('router')->generate('_cms_content', array('id' => $page->getId(), 'menu_id' => $menu_id, 'lang' => $lang, 'page_parent_id' => is_null($page_parent_id) ? "0" : $page_parent_id)));

            }
        }


        //Perform post submit
        $form = $this->createForm(new PageType($em, $this->_lang, $host_id), $page);
        if($request->getMethod() == 'POST'){

            $form->handleRequest($request);
            if ($form->isValid()) {
                $page = $form->getData();
                if(is_null($page->getId())){
                    $page->setUser($this->_user);

                }
                
                //Check if external page is set
                $external_link = $form['link_url2']->getData();
                if(!empty($external_link))
                    $page->setLinkUrl($external_link);

                if(!is_null($page_parent)) $page->setParent($page_parent);
                if(!is_null($menu)) $page->setMenu($menu);

                $em->persist($page);
                $em->flush();

                $pageLang = $form['lang']->getData();
                if(is_null($pageLang->getPage())){

                    $pageLang->setLocale($lang);
                    $pageLang->setPage($page);
                    $pageLang->setUser($this->_user);
                    $pageLang->setUrlRoot('');

                    $page->addLang($pageLang);
                }
         
                $em->persist($page);
                $em->flush();



                //Set routes to table
                $em->getRepository('MajesCmsBundle:Page')->generateRoutes($menu->getRef(), $this->_is_multilingual);

                return $this->redirect($this->get('router')->generate('_cms_content', array('id' => $page->getId(), 'menu_id' => $menu_id, 'lang' => $lang, 'page_parent_id' => is_null($page_parent_id) ? "0" : $page_parent_id)));

            }else{
                foreach ($form->getErrors() as $error) {
                    echo $message[] = $error->getMessage();
                }
            }

            
        }

        $form_role = null;
        if(!is_null($id))
            $form_role = $this->createForm(
                new PageRoleType($this->getDoctrine()->getManager(), $page),
                null,
                array('action' => $this->get('router')->generate('_cms_content_role', array('id' => $id, 'lang' => $lang)))
            )->createView();

        $edit = !is_null($id) ? 1 : 0;

        $pageSubTitle = is_null($page) ? $this->_translator->trans('Add a new page') : $this->_translator->trans('Edit page'). ' ' . (!is_null($page->getLang()) ? $page->getLang()->getTitle() : 'Language not yet available');

        return $this->render('MajesCmsBundle:Admin:content.html.twig', array(
            'pageTitle' => $this->_translator->trans('Content management'),
            'pageSubTitle' => $pageSubTitle,
            'form' => $form->createView(),
            'page' => $page,
            'edit' => $edit,
            'blocks' => $blocks,
            'page_has_draft' => $page_has_draft,
            'lang' => $lang,
            'form_role' => $form_role
            ));
    }

    /**
     * @Secure(roles="ROLE_CMS_PUBLISH, ROLE_SUPERADMIN")
     *
     */
    public function contentRoleAction($id, $lang){

        $request = $this->getRequest();

        $em = $this->getDoctrine()->getManager();
        $page = $em->getRepository('MajesCmsBundle:Page')
            ->findOneById($id);

        //Check permissions
        if(!Helper::hasAdminRole($page, $this->container->get('security.context')))
            throw new \Exception('Unauthorized access.', 403);

        $form_role = $this->createForm(
            new PageRoleType($this->getDoctrine()->getManager(), $page),
            null,
            array('action' => $this->get('router')->generate('_cms_content_role', array('id' => $id, 'lang' => $lang)))
        );

        if($request->getMethod() == 'POST'){
            $form_role->handleRequest($request);
            if ($form_role->isValid()) {

                $roles = $form_role->getData();
                $page->removeRoles();

                foreach ($roles as $bundle => $role_array) {
                    foreach($role_array as $role_id)
                    {
                        $role = $em->getRepository('MajesCoreBundle:User\Role')
                            ->findOneById($role_id);

                        $page->addRole($role);
                    }
                }

                $em = $this->getDoctrine()->getManager();

                $em->persist($page);
                $em->flush();

            }else{
                foreach ($form_role->getErrors() as $error) {
                    echo $message[] = $error->getMessage();
                }
       
            }
        }

        return $this->redirect($this->get('router')->generate('_cms_content', array('id' => $page->getId(), 'menu_id' => $page->getMenu()->getId(), 'lang' => $lang, 'page_parent_id' => is_null($page->getParent()) ? "0" : $page->getParent()->getId())));

    }

    /**
     * @Secure(roles="ROLE_CMS_PUBLISH,ROLE_SUPERADMIN")
     *
     */
    public function contentDeleteAction($id, $lang)
    {   

        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();

        //If lang is null, get default language
        if(is_null($lang)) $lang = $this->_lang;


        $page = $em->getRepository('MajesCmsBundle:Page')
            ->findOneById($id);
        
        if(is_null($page))
            die();

        //Check permissions
        if(!Helper::hasAdminRole($page, $this->container->get('security.context')))
            throw new \Exception('Unauthorized access.', 403);

        $page->setStatus('deleted');

        $em->persist($page);
        $em->flush();
        
        return $this->redirect($this->get('router')->generate('_cms_content', array('menu_id' => null, 'id' => null, 'lang' => null, 'page_parent_id' => null)));
    }

    /**
     * @Secure(roles="ROLE_CMS_PUBLISH,ROLE_SUPERADMIN")
     *
     */
    public function publishAction($id, $lang)
    {   

        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();

        //If lang is null, get default language
        if(is_null($lang)) $lang = $this->_lang;


        $page = $em->getRepository('MajesCmsBundle:Page')
            ->findOneById($id);
        
        if(is_null($page))
            die();

        //Check permissions
        if(!Helper::hasAdminRole($page, $this->container->get('security.context')))
            throw new \Exception('Unauthorized access.', 403);

        $page_parent = $page->getParent();
        $page_parent_id = null;
        if(!is_null($page_parent))
            $page_parent_id = $page_parent->getId();

        
        $pageTemplateBlocks = $page->getPageTemplateBlocks();
        foreach($pageTemplateBlocks as $pageTemplateBlock){
            if($pageTemplateBlock->getLocale() != $lang) continue;
            
            $draft = $pageTemplateBlock->getDraft();
            if(!is_null($draft)){
                $draft->setStatus('published');

                $em->persist($draft);
                $em->flush();

                $pageTemplateBlock->setContent($draft->getContent());
                $pageTemplateBlock->setVersion($draft->getVersion());

                $em->persist($pageTemplateBlock);
                $em->flush();

                //Log
                $logger = new Logger($em);
                $logger->log($this->_user, $this->_lang, 'Cms', $request->get('_route'), array('page_id' => $id));
            }
        }

        
        return $this->redirect($this->get('router')->generate('_cms_content', array('menu_id' => $page->getMenu()->getId(), 'id' => $page->getId(), 'lang' => $lang, 'page_parent_id' => is_null($page_parent_id) ? "0" : $page_parent_id)));
    }

    /**
     * @Secure(roles="ROLE_CMS_PUBLISH,ROLE_SUPERADMIN")
     *
     */
    public function discardDraftAction($id)
    {   

        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();


        $pageTemplateBlock = $em->getRepository('MajesCmsBundle:PageTemplateBlock')
            ->findOneById($id);
        
        if(is_null($pageTemplateBlock))
            die();

        $page = $pageTemplateBlock->getPage();
        $page_id = null;
        if(!is_null($page))
            $page_id = $page->getId();


        //Check permissions
        if(!Helper::hasAdminRole($page, $this->container->get('security.context')))
            throw new \Exception('Unauthorized access.', 403);

        $page_parent = $page->getParent();
        $page_parent_id = null;
        if(!is_null($page_parent))
            $page_parent_id = $page_parent->getId();

        
        
        $draft = $pageTemplateBlock->getDraft();
        if(!is_null($draft)){
 
            $em->remove($draft);
            $em->flush();

        }


        return $this->redirect($this->get('router')->generate('_cms_content', array('id' => $page_id, 'menu_id' => $page->getMenu()->getId(), 'lang' => $pageTemplateBlock->getLocale(), 'page_parent_id' => is_null($page_parent_id) ? "0" : $page_parent_id)));
    }

    /**
     * @Secure(roles="ROLE_CMS_CONTENT,ROLE_SUPERADMIN")
     */
    public function menuAction($id){

        $em = $this->getDoctrine()->getManager();

        //Get hosts
        $page = $em->getRepository('MajesCmsBundle:Page')
            ->findOneById($id);

        //Get hosts
        $hosts = $em->getRepository('MajesCoreBundle:Host')
            ->findAll();

        //Get menus
        $navs = $em->getRepository('MajesCmsBundle:Menu')
            ->findAll();

        $menu = array();
        foreach($hosts as $host){
            foreach($navs as $nav){

                $response = $em->getRepository('MajesCmsBundle:Page')
                    ->getMenu($host->getId(), 'fr', $nav->getRef());
    
                $response = array_values( (array)$response );
                $menu[$host->getId()][$nav->getRef()]['data'] = $nav;
                $menu[$host->getId()][$nav->getRef()]['tree'] = json_encode($response);
            }

        }

        return $this->render('MajesCmsBundle:Admin:parts/tree.html.twig', array(
            'page_id' => $id,
            'page' => $page,
            'hosts' => $hosts,
            'menu' => $menu
            ));
    }


    /**
     * @Secure(roles="ROLE_CMS_DESIGNER,ROLE_SUPERADMIN")
     *
     */
    public function blocksAction()
    {
        
        $em = $this->getDoctrine()->getManager();
        $blocks = $em->getRepository('MajesCmsBundle:Block')
            ->findAll();
        
        return $this->render('MajesCoreBundle:common:datatable.html.twig', array(
            'datas' => $blocks,
            'object' => new Block(),
            'pageTitle' => $this->_translator->trans('Content management'),
            'pageSubTitle' => $this->_translator->trans('List of all available blocks?'),
            'urls' => array(
                'add' => '_cms_block_edit',
                'edit' => '_cms_block_edit',
                'delete' => '_cms_block_delete'
                )
            ));
    }

    /**
     * @Secure(roles="ROLE_CMS_DESIGNER,ROLE_SUPERADMIN")
     *
     */
    public function blockEditAction($id)
    {
        
        $request = $this->getRequest();

        $em = $this->getDoctrine()->getManager();
        $block = $em->getRepository('MajesCmsBundle:Block')
            ->findOneById($id);


        $form = $this->createForm(new BlockType(), $block);

        if($request->getMethod() == 'POST'){

            $form->handleRequest($request);
            if ($form->isValid()) {

                if(is_null($block)) $block = $form->getData();

                $block->setUser($this->_user);

                //Get attributes from form data
                $attributes_tmp = $request->request->get('attributes');
                $ref = $request->request->get('ref');
                $title = $request->request->get('title');

                $attributes_tmp = is_null($attributes_tmp) ? array() : $attributes_tmp;
                if(!is_null($attributes_tmp)){
                    //Get all already existant block_attribute_ids that are in the form, in order to keep them
                    $block_attributes_ids = array();
                    foreach($attributes_tmp as $attributes)
                        foreach ($attributes as $attribute_id => $block_attributes) {
                            foreach ($block_attributes as $block_attribute_id) {
                                if($block_attribute_id != 0) $block_attributes_ids[] = $block_attribute_id;
                            }
                        }

                    //Get all block_attributes from object block
                    $old_attributes_temp = $block->getBlockAttributes();
    
                    //Parse them all in order to keep them or remove them
                    $old_attributes_array = array();
                    foreach ($old_attributes_temp as $old_attribute) {
                        if(!in_array($old_attribute->getId(), $block_attributes_ids)){
                            $block->removeBlockAttribute($old_attribute);
                            
                            $blockAttribute = $em->getRepository('MajesCmsBundle:BlockAttribute')
                                    ->findOneById($old_attribute->getId());
                            if($blockAttribute){
                                $em->remove($blockAttribute);
                                $em->flush();
                            }
                        }else
                            $old_attributes_array[] = $old_attribute->getId();
                    }
    
                    /*SET ATTRIBUTES*/
                    //$block->removeAttributes();
                    $sort = 10;
                    foreach($attributes_tmp as $attributes)
                        foreach ($attributes as $attribute_id => $block_attributes) {
                            foreach ($block_attributes as $block_attribute_id) {
                                if($block_attribute_id != 0){
                                    $blockAttribute = $em->getRepository('MajesCmsBundle:BlockAttribute')
                                        ->findOneById($block_attribute_id);
                                    
                                    $blockAttribute->setSort($sort);
                                    if(!empty($ref[$block_attribute_id])) $blockAttribute->setRef($ref[$block_attribute_id]);
                                    if(!empty($title[$block_attribute_id])) $blockAttribute->setTitle($title[$block_attribute_id]);
        

                                    $em->persist($blockAttribute);
                                    $em->flush();
                                }else{
                                    $attribute = $em->getRepository('MajesCmsBundle:Attribute')
                                        ->findOneById($attribute_id);
        
                                    $blockAttribute = new BlockAttribute();
                                    $blockAttribute->setSort($sort);
                                    $blockAttribute->setBlock($block);
                                    $blockAttribute->setAttribute($attribute);
                                    $blockAttribute->setRef($block->getRef().'_'.$attribute->getRef());
        
                                    $em->persist($blockAttribute);
                                    $em->flush();
        
                                    $block->addBlockAttribute($blockAttribute);
                                }
                                $sort += 10;
                            }
                        }
                }
                

                $em->persist($block);
                $em->flush();

                return $this->redirect($this->get('router')->generate('_cms_block_edit', array('id' => $block->getId())));

            }else{
                foreach ($form->getErrors() as $error) {
                    echo $message[] = $error->getMessage();
                }
            }
        }

        /*GET ATTRIBUTE LIST*/
        $attributes = $em->getRepository('MajesCmsBundle:Attribute')
            ->findAll();

        $pageSubTitle = empty($block) ? $this->_translator->trans('Add a new block') : $this->_translator->trans('Edit block'). ' ' . $block->getTitle();
        
        //var_dump($block->getAttributes()); exit;

        return $this->render('MajesCmsBundle:Admin:block-edit.html.twig', array(
            'pageTitle' => $this->_translator->trans('Content management'),
            'pageSubTitle' => $pageSubTitle,
            'block' => $block,
            'form' => $form->createView(),
            'attributes' => $attributes
            ));
    }

        /**
     * @Secure(roles="ROLE_CMS_DESIGNER,ROLE_SUPERADMIN")
     *
     */
    public function blockDeleteAction($id) {

        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();

        $block = $em->getRepository('MajesCmsBundle:Block')
                ->findOneById($id);

        if (!is_null($block)) {
            $em->remove($block);
            $em->flush();
        }
        
        return $this->redirect($this->get('router')->generate('_cms_blocks_list', array()));
    }
    
    /**
     * @Secure(roles="ROLE_CMS_DESIGNER,ROLE_SUPERADMIN")
     *
     */
    public function templatesAction()
    {   
        $em = $this->getDoctrine()->getManager();
        $templates = $em->getRepository('MajesCmsBundle:Template')
            ->findAll();
        
        return $this->render('MajesCoreBundle:common:datatable.html.twig', array(
            'datas' => $templates,
            'object' => new Template(),
            'pageTitle' => $this->_translator->trans('Content management'),
            'pageSubTitle' => $this->_translator->trans('List of all available templates?'),
            'urls' => array(
                'add' => '_cms_template_edit',
                'edit' => '_cms_template_edit',
                'delete' => '_cms_template_delete'
                )
            ));

    }

    /**
     * @Secure(roles="ROLE_CMS_DESIGNER,ROLE_SUPERADMIN")
     *
     */
    public function templateEditAction($id)
    {
        
        $request = $this->getRequest();

        $em = $this->getDoctrine()->getManager();
        $template = $em->getRepository('MajesCmsBundle:Template')
            ->findOneById($id);


        $form = $this->createForm(new TemplateType(), $template);

        if($request->getMethod() == 'POST'){

            $form->handleRequest($request);
            if ($form->isValid()) {

                if(is_null($template)) $template = $form->getData();

                $template->setUser($this->_user);

                //Get attributes from form data
                $blocks_tmp = $request->request->get('blocks');
                $ref = $request->request->get('ref');
                $title = $request->request->get('title');
                $blocks_tmp = is_null($blocks_tmp) ? array() : $blocks_tmp;
                //Get all already existant template_blocks_ids that are in the form, in order to keep them
                if(!is_null($blocks_tmp)){
                    $template_blocks_ids = array();
                    foreach($blocks_tmp as $blocks)
                        foreach ($blocks as $block_id => $template_blocks) {
                            foreach ($template_blocks as $template_block_id) {
                                if($template_block_id != 0) $template_blocks_ids[] = $template_block_id;
                            }
                        }
    
                    //Get all template_blocks from object block
                    $old_blocks_temp = $template->getTemplateBlocks();
    
                    //Parse them all in order to keep them or remove them
                    $old_blocks_array = array();
                    foreach ($old_blocks_temp as $old_block) {
                        if(!in_array($old_block->getId(), $template_blocks_ids)){
                            $template->removeTemplateBlock($old_block);
                            
                            $templateBlock = $em->getRepository('MajesCmsBundle:TemplateBlock')
                                    ->findOneById($old_block->getId());
                            if($templateBlock){
                                $em->remove($templateBlock);
                                $em->flush();
                            }
                        }else
                            $old_blocks_array[] = $old_block->getId();
                    }
    
                    /*SET ATTRIBUTES*/
                    $sort = 10;
                    foreach($blocks_tmp as $blocks)
                        foreach ($blocks as $block_id => $template_blocks) {
                            foreach ($template_blocks as $template_block_id) {
                                if($template_block_id != 0){
                                    $templateBlock = $em->getRepository('MajesCmsBundle:TemplateBlock')
                                        ->findOneById($template_block_id);
        
                                    $templateBlock->setSort($sort);
                                    if(!empty($ref[$template_block_id])) $templateBlock->setRef($ref[$template_block_id]);
                                    if(!empty($title[$template_block_id])) $templateBlock->setTitle($title[$template_block_id]);
        

                                    $em->persist($templateBlock);
                                    $em->flush();
                                }else{
                                    $block = $em->getRepository('MajesCmsBundle:Block')
                                        ->findOneById($block_id);
        
                                    $templateBlock = new TemplateBlock();
                                    $templateBlock->setSort($sort);
                                    $templateBlock->setBlock($block);
                                    $templateBlock->setTemplate($template);
                                    $templateBlock->setRef($template->getRef().'_'.$block->getRef());
    
                                    $em->persist($templateBlock);
                                    $em->flush();
        
                                    $template->addTemplateBlock($templateBlock);
                                }
                                $sort += 10;
                            }
                        }
                }

                $em->persist($template);
                $em->flush();

                return $this->redirect($this->get('router')->generate('_cms_template_edit', array('id' => $template->getId())));

            }else{
                foreach ($form->getErrors() as $error) {
                    echo $message[] = $error->getMessage();
                }
            }
        }

        /*GET ATTRIBUTE LIST*/
        $blocks = $em->getRepository('MajesCmsBundle:Block')
            ->findAll();

        $pageSubTitle = empty($block) ? $this->_translator->trans('Add a new template') : $this->_translator->trans('Edit template'). ' ' . $block->getTitle();
        
        //var_dump($block->getAttributes()); exit;

        return $this->render('MajesCmsBundle:Admin:template-edit.html.twig', array(
            'pageTitle' => $this->_translator->trans('Content management'),
            'pageSubTitle' => $pageSubTitle,
            'template' => $template,
            'form' => $form->createView(),
            'blocks' => $blocks
            ));
    }

    /**
     * @Secure(roles="ROLE_CMS_DESIGNER,ROLE_SUPERADMIN")
     *
     */
    public function templateBlockEditAction($id)
    {
        
        $request = $this->getRequest();

        $em = $this->getDoctrine()->getManager();
        $template_block = $em->getRepository('MajesCmsBundle:TemplateBlock')
            ->findOneById($id);

        if($request->isXmlHttpRequest()){
            if(!is_null($template_block)){
                $column = $request->get('column');
                switch ($column) {
                    case 'isMobile':
                        $template_block->setIsMobile($template_block->getIsMobile() ? 0 : 1);
                        break;
                    
                    case 'isTablet':
                        $template_block->setIsTablet($template_block->getIsTablet() ? 0 : 1);
                        break;

                    case 'isDesktop':
                        $template_block->setIsDesktop($template_block->getIsDesktop() ? 0 : 1);
                        break;

                    case 'isRepeatable':
                        $template_block->setIsRepeatable($template_block->getIsRepeatable() ? 0 : 1);
                        break;

                    default:
                        # code...
                        break;
                }

                $em->persist($template_block);
                $em->flush();

                echo json_encode(array('error' => false, 'message' => $this->_translator->trans('The block has been updated successfully')));
            }else{
                echo json_encode(array('error' => true, 'message' => $this->_translator->trans('An error has occured during saving')));
            }
        }

        return new Response();
    }
    
    /**
     * @Secure(roles="ROLE_CMS_DESIGNER,ROLE_SUPERADMIN")
     *
     */
    public function templateDeleteAction($id){
     
        
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();

        $template = $em->getRepository('MajesCmsBundle:Template')
                ->findOneById($id);

        if (!is_null($template)) {
            $em->remove($template);
            $em->flush();
        }

        return $this->redirect($this->get('router')->generate('_cms_templates_list', array()));
    }

    /**
     * @Secure(roles="ROLE_CMS_CONTENT,ROLE_SUPERADMIN")
     */
    public function menuOrderAction($host_id){

        $request = $this->getRequest();
        if($request->isXmlHttpRequest()){

            $page_id = $request->get('page_id');
            $parent_id = $request->get('parent_id');
            $position = $request->get('position');
            $ids = $request->get('ids');

            $em = $this->getDoctrine()->getManager();
            
            //Set parent        
            $page = $em->getRepository('MajesCmsBundle:Page')
                ->findOneById($page_id);

            $parent = ($parent_id != 0) ? $em->getRepository('MajesCmsBundle:Page')
                ->findOneById($parent_id) : null;

            $page->setParent($parent);
            
            //Set order
            $sort = 0;
            foreach($ids as $id){

                if($id == $page_id) $page->setSort($sort);
                else{
                    $page_sibling = $em->getRepository('MajesCmsBundle:Page')
                        ->findOneById($id);

                    $page_sibling->setSort($sort);
                    $em->persist($page_sibling);
                    $em->flush();
                }

                $sort += 10;
            }

            $em->persist($page);
            $em->flush();

            //Set routes to table
            $em->getRepository('MajesCmsBundle:Page')->generateRoutes($page->getMenu()->getRef(), $this->_is_multilingual);
            
        }

        return new Response(json_encode(array('error' => false)));
    }

    /**
     * @Secure(roles="ROLE_CMS_CONTENT,ROLE_SUPERADMIN")
     */
    public function pageBlockOrderAction(){

        $request = $this->getRequest();
        if($request->isXmlHttpRequest()){

            $page_template_block_id = $request->get('page_template_block_id');
            $ids = $request->get('ids');

            $em = $this->getDoctrine()->getManager();
            
            //Set parent        
            $pageTemplateBlock = $em->getRepository('MajesCmsBundle:PageTemplateBlock')
                ->findOneById($page_template_block_id);

            $content = $pageTemplateBlock->getContent();
            $content_array = json_decode($content, true);

            $new_content = array();
            foreach($ids as $id){

                if(isset($content_array['attributes'][$id])) $new_content[$id] = $content_array['attributes'][$id];

            }

            $content = json_encode(array('attributes' => $new_content));
            $pageTemplateBlock->setContent($content);
            $em->persist($pageTemplateBlock);
            $em->flush();

        }

        return new Response();
    }

    /**
     * @Secure(roles="ROLE_CMS_CONTENT,ROLE_SUPERADMIN")
     */
    public function pageBlockFormAction($lang){

        $request = $this->getRequest();
        
        if($request->isXmlHttpRequest()){

            $page_template_block_id = $request->get('page_template_block_id');
            $page_id = $request->get('page_id');
            $template_block_id = $request->get('template_block_id');
            $id = $request->get('id');
            $wysiwyg = $request->get('wysiwyg', false);
            
            $em = $this->getDoctrine()->getManager();
            
            //Set pageTemplateBlock        
            $pageTemplateBlock = $em->getRepository('MajesCmsBundle:PageTemplateBlock')
                ->findOneById($page_template_block_id);

            $page = $em->getRepository('MajesCmsBundle:Page')
                ->findOneById($page_id);

            //Check permissions
            if(!Helper::hasAdminRole($page, $this->container->get('security.context')))
                throw new \Exception('Unauthorized access.', 403);

            $templateBlock = $em->getRepository('MajesCmsBundle:TemplateBlock')
                ->findOneById($template_block_id);

            $block = $em->getRepository('MajesCmsBundle:Page')
                ->getBlock($page, $templateBlock, $lang, $id);

            return $this->render('MajesCmsBundle:Admin:parts/form-block.html.twig', array(
                'pageTemplateBlock' => $pageTemplateBlock,
                'page' => $page,
                'templateBlock' => $templateBlock,
                'lang' => $lang,
                'block' => $block,
                'id' => $id,
                'wysiwyg' => $wysiwyg
            ));

        }else
            return new Response();
    }

    /**
     * @Secure(roles="ROLE_CMS_CONTENT,ROLE_SUPERADMIN")
     */
    public function pageBlockEditAction(){

        $request = $this->getRequest();
        if($request->getMethod() == 'POST'){

            $page_template_block_id = $request->get('page_template_block_id');
            $page_id = $request->get('page_id');
            $id = $request->get('id');
            $template_block_id = $request->get('template_block_id');
            $lang = $request->get('lang');
            $title = $request->get('title', '');
            $wysiwyg = $request->get('wysiwyg', false);

            $attributes = $request->get('attributes');

            
            $em = $this->getDoctrine()->getManager();
            
            //Set pageTemplateBlock        
            $pageTemplateBlock = $em->getRepository('MajesCmsBundle:PageTemplateBlock')
                ->findOneById($page_template_block_id);

            $page = $em->getRepository('MajesCmsBundle:Page')
                ->findOneById($page_id);



            //Check permissions
            if(!Helper::hasAdminRole($page, $this->container->get('security.context')))
                throw new \Exception('Unauthorized access.', 403);

            $templateBlock = $em->getRepository('MajesCmsBundle:TemplateBlock')
                ->findOneById($template_block_id);

            $block = $em->getRepository('MajesCmsBundle:Page')
                ->getBlock($page, $templateBlock, $lang, $id);

            $pageLang = $em->getRepository('MajesCmsBundle:PageLang')
                ->findOneBy(array('page' => $page, 'locale' => $lang));


            //If content does not exists we push it to published directly
            if(is_null($pageTemplateBlock)){
                $pageTemplateBlock = new PageTemplateBlock();
                $pageTemplateBlock->setPage($page);
                $pageTemplateBlock->setTemplateBlock($templateBlock);
                $pageTemplateBlock->setUser($this->_user);
                $pageTemplateBlock->setLocale($lang);
                $pageTemplateBlock->setVersion(1);

                $datatype = new Datatype($pageTemplateBlock, $block, $attributes, $em, $request, $this->_user, $id, $title);
                $pageTemplateBlock->setContent(json_encode($datatype->_content));
    
                $em->persist($pageTemplateBlock);
                $em->flush();
            }elseif($wysiwyg == 1){
                $draft = $pageTemplateBlock->getDraft();

                $datatype = new Datatype($pageTemplateBlock, $block, $attributes, $em, $request, $this->_user, $id, $title);
                $pageTemplateBlock->setContent(json_encode($datatype->_content));
                
                if(!is_null($draft)){
                    $pageTemplateBlock->setVersion($draft->getVersion());

                    $draft->setContent(json_encode($datatype->_content));
                    $draft->setStatus('published');

                    $em->persist($draft);
                    $em->flush();
                }

                $em->persist($pageTemplateBlock);
                $em->flush();

            }
            else{

                $draft = $pageTemplateBlock->getDraft();
                if(is_null($draft)){
                    $version = $pageTemplateBlock->getLastVersion()+1;

                    $draft = new PageTemplateBlockVersion();
                    $draft->setVersion($version);
                    $draft->setUser($this->_user);
                    $draft->setPageTemplateBlock($pageTemplateBlock);
                    $draft->setLocale($pageTemplateBlock->getLocale());

                    $datatype = new Datatype($pageTemplateBlock, $block, $attributes, $em, $request, $this->_user, $id, $title);
                
                }else{
                    $datatype = new Datatype($draft, $block, $attributes, $em, $request, $this->_user, $id, $title);
                }

                $draft->setContent(json_encode($datatype->_content));

                $em->persist($draft);
                $em->flush();
            }  

            //Hack to index content
            $pageLang->setUpdateDate(new \DateTime());
            $em->persist($pageLang);
            $em->flush();        

            if($wysiwyg)
                return $this->redirect($this->get('router')->generate('majes_cms_'.$page_id.'_'.$lang));
            else
                return $this->redirect($this->get('router')->generate('_cms_content', array('id' => $page->getId(), 'menu_id' => $page->getMenu()->getId(), 'lang' => $lang, 'page_parent_id' => is_null($page->getParent()) ? "0" : $page->getParent()->getId())));


        }else
            return new Response();
    }
    
    /**
     * @Secure(roles="ROLE_CMS_CONTENT,ROLE_SUPERADMIN")
     */
    public function pageBlockDeleteAction(){


        $request = $this->getRequest();
        if($request->getMethod() == 'POST'){

            $page_template_block_id = $request->get('page_template_block_id');
            $page_id = $request->get('page_id');
            $id = $request->get('id');
            $template_block_id = $request->get('template_block_id');
            $lang = $request->get('lang');
            $title = $request->get('title', '');
            $wysiwyg = $request->get('wysiwyg', false);



            
            $em = $this->getDoctrine()->getManager();
            
            //Set pageTemplateBlock        
            $pageTemplateBlock = $em->getRepository('MajesCmsBundle:PageTemplateBlock')
                ->findOneById($page_template_block_id);
            $attributes=json_decode($pageTemplateBlock->getContent(),true);
        


            $page = $em->getRepository('MajesCmsBundle:Page')
                ->findOneById($page_id);



            //Check permissions
            if(!Helper::hasAdminRole($page, $this->container->get('security.context')))
                throw new \Exception('Unauthorized access.', 403);

            $templateBlock = $em->getRepository('MajesCmsBundle:TemplateBlock')
                ->findOneById($template_block_id);

            $block = $em->getRepository('MajesCmsBundle:Page')
                ->getBlock($page, $templateBlock, $lang, $id);

            $pageLang = $em->getRepository('MajesCmsBundle:PageLang')
                ->findOneBy(array('page' => $page, 'locale' => $lang));



            
            $draft = $pageTemplateBlock->getDraft();

            unset($attributes['attributes'][$id]);
            $pageTemplateBlock->setContent(json_encode($attributes));
            
            if(!is_null($draft)){
                $pageTemplateBlock->setVersion($draft->getVersion());

                $draft->setContent(json_encode($attributes));
                $draft->setStatus('published');

                $em->persist($draft);
                $em->flush();
            }

            $em->persist($pageTemplateBlock);
            $em->flush();

            //Hack to index content
            $pageLang->setUpdateDate(new \DateTime());
            $em->persist($pageLang);
            $em->flush();        
            
            return new Response(json_encode(array("success" => true)));

        }else
            return new Response(json_encode(array("success" => false)));
    }
    /**
     * @Secure(roles="ROLE_CMS_PUBLISH, ROLE_SUPERADMIN")
     *
     */
    public function rolesAction(){
        $em = $this->getDoctrine()->getManager();
        $roles = $em->getRepository('MajesCoreBundle:User\Role')
            ->findBy(array(
                'bundle' => 'cms'));

        return $this->render('MajesCoreBundle:common:datatable.html.twig', array(
            'datas' => $roles,
            'object' => new Role(),
            'pageTitle' => $this->_translator->trans('Roles'),
            'pageSubTitle' => $this->_translator->trans('List off all roles currently available'),
            'urls' => array(
                'add' => '_cms_role_edit',
                'edit' => '_cms_role_edit',
                'delete' => '_cms_role_delete'
                )
            ));
    }

    /**
     * @Secure(roles="ROLE_CMS_PUBLISH, ROLE_SUPERADMIN")
     *
     */
    public function roleEditAction($id){

        $request = $this->getRequest();

        $em = $this->getDoctrine()->getManager();
        $role = $em->getRepository('MajesCoreBundle:User\Role')
            ->findOneById($id);


        $form = $this->createForm(new RoleType(), $role);
        $form->get('bundle')->setData('cms');
        
        if($request->getMethod() == 'POST'){

            $form->handleRequest($request);
            if ($form->isValid()) {

                if(is_null($role)) $role = $form->getData();

                $em = $this->getDoctrine()->getManager();
                $em->persist($role);
                $em->flush();

                return $this->redirect($this->get('router')->generate('_cms_role_edit', array('id' => $role->getId())));

            }else{
                foreach ($form->getErrors() as $error) {
                    echo $message[] = $error->getMessage();
                }
            }
        }

        $pageSubTitle = empty($role) ? $this->_translator->trans('Add a new role') : $this->_translator->trans('Edit role'). ' ' . $role->getRole();
        
        return $this->render('MajesCoreBundle:Index:role-edit.html.twig', array(
            'pageTitle' => $this->_translator->trans('Roles'),
            'pageSubTitle' => $pageSubTitle,
            'form' => $form->createView()));
    }

    /**
     * @Secure(roles="ROLE_CMS_PUBLISH, ROLE_SUPERADMIN")
     *
     */
    public function roleDeleteAction($id){
        $request = $this->getRequest();

        $em = $this->getDoctrine()->getManager();
        $role = $em->getRepository('MajesCoreBundle:User\Role')
            ->findOneById($id);

        if(!is_null($role)){
            $em->remove($role);
            $em->flush();
        }


        return $this->redirect($this->get('router')->generate('_cms_roles', array()));
    }

}
