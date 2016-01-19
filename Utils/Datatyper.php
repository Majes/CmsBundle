<?php
namespace Majes\CmsBundle\Utils;

use Majes\MediaBundle\Entity\Media;

class Datatyper
{

    public $_content;
    public $_ref;
    public $_em;
    public $_request;
    public $_user;
    public $_block;
    public $_mediaService;

    public function __construct($pageTemplateBlock, $block, $attributes, $em, $request, $user, $id = '', $title = '', $mediaService = null){

        $this->_em = $em;
        $this->_request = $request;
        $this->_user = $user;
        $this->_block = $block;
        $this->_mediaService = $mediaService;

        $this->_content = !is_null($pageTemplateBlock) ? json_decode($pageTemplateBlock->getContent(), true) : array('attributes' => array());

        //Id of attribute content
        if(empty($id)) $id = date('YmdHis');

        $this->_content['attributes'][$id] = array(
            'title' => $title,
            'id' => $id);

        $value = array();
        foreach($attributes as $ref => $attribute){

            $block_attribute_id = $attribute['id'];
            unset($attribute['id']);

            $method = $attribute['ref'];
            $attribute = $this->$method($attribute, $ref);

            $value[$ref] = $attribute;
        }
        $this->_content['attributes'][$id]['content'] = $value;

    }

    public function picture($attribute, $ref){

        if(isset($attribute['remove']) && $attribute['remove']) return null;

        $file = $this->_request->files->get('attributes');
        $file = $file[$ref]['value'];

        unset($attribute['value']);

        $media = $this->_em->getRepository('MajesMediaBundle:Media')
            ->findOneById($attribute['media_id']);

        if (is_null($media) && is_null($file)) return null;

        if(is_null($media)){
            $media = new Media();
            $media->setCreateDate(new \DateTime(date('Y-m-d H:i:s')));
            $media->setUser($this->_user);
            $media->setFolder('Cms');
            $media->setType('picture');
        }

        if(!is_null($file)
            || is_null($media)){
            $media = new Media();
            $media->setCreateDate(new \DateTime(date('Y-m-d H:i:s')));
            $media->setUser($this->_user);
            $media->setFolder('Cms');
            $media->setType('picture');
            $media->setFile($file);
        }

        $title = $media->getTitle();
        $author = $media->getAuthor();
        if(empty($title)) $media->setTitle($attribute['title']);
        if(empty($author)) $media->setAuthor($attribute['author']);

        $this->_em->persist($media);
        $this->_em->flush();

        $is_protected = $media->getIsProtected();
        $attribute['path'] = $is_protected ? '' : $media->getWebPath();
        $attribute['media_id'] = $media->getId();
        return $attribute;
    }

    public function media($attribute, $ref){

        if(isset($attribute['remove']) && $attribute['remove']) return null;

        unset($attribute['value']);

        $media = $this->_em->getRepository('MajesMediaBundle:Media')
            ->findOneById($attribute['media_id']);

        if (is_null($media)) return null;

        $is_protected = $media->getIsProtected();
        $attribute['path'] = $is_protected ? '' : $media->getWebPath();
        $attribute['media_id'] = $media->getId();
        $attribute['type'] = $media->getType();
        return $attribute;
    }

    public function textline($attribute, $ref){
        return $attribute;
    }

    public function date($attribute, $ref){
        return $attribute;
    }

    public function listbox($attribute, $ref){
        return $attribute;
    }

    public function selection($attribute, $ref){
        return $attribute;
    }

    public function listboxmultiple($attribute, $ref){
        return $attribute;
    }

    public function textarea($attribute, $ref){
        return $attribute;
    }

    public function editor($attribute, $ref){
        return $attribute;
    }

    public function checkbox($attribute, $ref){
        return $attribute;
    }

    public function link($attribute, $ref){

        $url = $attribute['value'];

        if(!empty($attribute['internal'])){
            $url = $this->_em->getRepository('MajesCmsBundle:Route')->findOneById($attribute['internal'])->getUrl();
            $attribute['value'] = $url;
        }

        $route = $this->_em->getRepository('MajesCmsBundle:Route')
            ->findOneBy(array('url' => $url));

        if(!is_null($route))
        {
            $page = $route->getPage();
            if(!empty($route->getPage())){
                $attribute['page_id'] = $page->getId();
                $attribute['page_icon'] = $page->getIcon();
            }
            $attribute['locale'] = $route->getLocale();
            $attribute['route_id'] = $route->getId();
        }
        return $attribute;
    }

    public function internallink($attribute, $ref){

        $id = $attribute['value'];
        if (empty($id)) {
            $attribute = false;
            return $attribute;
        }

        $route = $this->_em->getRepository('MajesCmsBundle:Route')
            ->findOneBy(array('id' => $id));
        $routebis = $this->_em->getRepository('MajesCmsBundle:PageLang')
            ->findOneBy(array('page' => $route->getPage(), 'locale' => $route->getLocale()));

        if(!is_null($route))
        {
            $page = $route->getPage();
            if(!empty($page)){
                $attribute['page_id'] = $page->getId();
                $attribute['page_icon'] = $page->getIcon();
            }
            $attribute['page_title'] = $routebis->getTitle();

        }

        return $attribute;
    }

    public function file($attribute, $ref){

        if(isset($attribute['remove']) && $attribute['remove']) return null;

        $file = $this->_request->files->get('attributes');
        $file = $file[$ref]['value'];

        unset($attribute['value']);

        $media = $this->_em->getRepository('MajesMediaBundle:Media')
            ->findOneById($attribute['media_id']);

        if (is_null($media) && is_null($file)) return null;

        if(is_null($media)){
            $media = new Media();
            $media->setCreateDate(new \DateTime(date('Y-m-d H:i:s')));
            $media->setUser($this->_user);
            $media->setFolder('Cms');
            $media->setType('document');
        }

        if(!is_null($file)
            || is_null($media)){
            $media = new Media();
            $media->setCreateDate(new \DateTime(date('Y-m-d H:i:s')));
            $media->setUser($this->_user);
            $media->setFolder('Cms');
            $media->setType('document');
            $media->setFile($file);
        }

        $title = $media->getTitle();
        $author = $media->getAuthor();
        if(empty($title)) $media->setTitle($attribute['title']);
        if(empty($author)) $media->setAuthor($attribute['author']);

        $this->_em->persist($media);
        $this->_em->flush();

        $is_protected = $media->getIsProtected();
        $attribute['path'] = $is_protected ? '' : $media->getWebPath();
        $attribute['media_id'] = $media->getId();
        return $attribute;
    }

    public function video($attribute, $ref){

        if(isset($attribute['remove']) && $attribute['remove']) return null;

        $file = $this->_request->files->get('attributes');
        $file = $file[$ref]['value'];

        unset($attribute['value']);

        $media = $this->_em->getRepository('MajesMediaBundle:Media')
            ->findOneById($attribute['media_id']);

        if (is_null($media) && is_null($file)) return null;

        if(is_null($media)){
            $media = new Media();
            $media->setCreateDate(new \DateTime(date('Y-m-d H:i:s')));
            $media->setUser($this->_user);
            $media->setFolder('Cms');
            $media->setType('video');
        }

        if(!is_null($file)
            || is_null($media)){
            $media = new Media();
            $media->setCreateDate(new \DateTime(date('Y-m-d H:i:s')));
            $media->setUser($this->_user);
            $media->setFolder('Cms');
            $media->setType('video');
            $media->setFile($file);
        }

        $title = $media->getTitle();
        $author = $media->getAuthor();
        if(empty($title)) $media->setTitle($attribute['title']);
        if(empty($author)) $media->setAuthor($attribute['author']);

        $this->_em->persist($media);
        $this->_em->flush();

        $is_protected = $media->getIsProtected();
        $attribute['path'] = $is_protected ? '' : $media->getWebPath();
        $attribute['media_id'] = $media->getId();
        return $attribute;
    }
}
