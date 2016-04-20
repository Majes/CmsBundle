<?php
namespace Majes\CmsBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Majes\CoreBundle\Annotation\DataTable;


/**
 * Majes\CmsBundle\Entity\PageLang
 *
 * @ORM\Table(name="cms_page_lang", indexes={
 *      @ORM\Index(name="locale", columns={"locale"}),
 *      @ORM\Index(name="is_active", columns={"is_active"})
 * })
 * @ORM\Entity(repositoryClass="Majes\CmsBundle\Entity\PageLangRepository")
 * @ORM\HasLifecycleCallbacks
 */
class PageLang{


    /**
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Majes\CmsBundle\Entity\Page", inversedBy="langs", cascade={"persist"})
     * @ORM\JoinColumn(name="page_id", referencedColumnName="id", nullable=false)
     */
    private $page;

    /**
     * @ORM\Column(name="locale", type="string", length=5, nullable=false)
     */
    private $locale;

    /**
     * @ORM\ManyToOne(targetEntity="Majes\CoreBundle\Entity\User\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(name="title", type="string", length=150, nullable=false)
     */
    private $title;

    /**
     * @ORM\Column(name="url", type="string", length=150, nullable=false)
     */
    private $url;

    /**
     * @ORM\Column(name="url_root", type="string", length=255, nullable=true)
     */
    private $urlRoot=null;

    /**
     * @ORM\Column(name="meta_description", type="string", length=255, nullable=true)
     */
    private $metaDescription=null;

    /**
     * @ORM\Column(name="meta_keywords", type="string", length=255, nullable=true)
     */
    private $metaKeywords=null;

    /**
     * @ORM\Column(name="meta_title", type="string", length=150, nullable=true)
     */
    private $metaTitle=null;

    /**
     * @ORM\Column(name="meta_canonical", type="string", length=255, nullable=true)
     */
    private $metaCanonical=null;

    /**
     * @ORM\Column(name="create_date", type="datetime", nullable=false)
     */
    private $createDate;

    /**
     * @ORM\Column(name="update_date", type="datetime", nullable=false)
     */
    private $updateDate;

    /**
     * @ORM\Column(name="tags", type="string", length=150, nullable=false)
     */
    private $tags='Page';

    /**
     * @ORM\Column(name="search_description", type="text", nullable=true)
     */
    private $searchDescription;

    /**
     * search index purpose
     */
    private $content;

    /**
     * @ORM\Column(name="is_active", type="boolean", nullable=false)
     */
    private $isActive=1;

    /**
     * @ORM\Column(name="deleted", type="boolean", nullable=false)
     */
    private $deleted=0;

    private $indexType;


    /**
     * @inheritDoc
     */
    public function __construct(){
        $this->createDate = new \DateTime();
        $this->url_root = '';
        $this->tags = 'Page';
    }

    /**
     * @inheritDoc
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setUser(\Majes\CoreBundle\Entity\User\User $user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setPage(\Majes\CmsBundle\Entity\Page $page)
    {
        $this->page = $page;
        return $this;
    }



    /**
     * @inheritDoc
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setUpdateDate($updateDate)
    {
        $this->updateDate = $updateDate;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setCreateDate($createDate)
    {
        $this->createDate = $createDate;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setUrlRoot($urlRoot)
    {
        $this->urlRoot = $urlRoot;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setMetaTitle($metaTitle)
    {
        $this->metaTitle = $metaTitle;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setMetaCanonical($metaCanonical)
    {
        $this->metaCanonical = $metaCanonical;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setMetaDescription($metaDescription)
    {
        $this->metaDescription = $metaDescription;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setMetaKeywords($metaKeywords)
    {
        $this->metaKeywords = $metaKeywords;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setSearchDescription($searchDescription)
    {
        $this->searchDescription = $searchDescription;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @inheritDoc
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @inheritDoc
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @inheritDoc
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @inheritDoc
     */
    public function getUrlRoot()
    {
        return $this->urlRoot;
    }

    /**
     * @inheritDoc
     */
    public function getMetaTitle()
    {
        return $this->metaTitle;
    }

    /**
     * @inheritDoc
     */
    public function getMetaCanonical()
    {
        return $this->metaCanonical;
    }

    /**
     * @inheritDoc
     */
    public function getMetaDescription()
    {
        return $this->metaDescription;
    }

    /**
     * @inheritDoc
     */
    public function getMetaKeywords()
    {
        return $this->metaKeywords;
    }

    /**
     * @inheritDoc
     */
    public function getCreateDate()
    {
        return $this->createDate;
    }

    /**
     * @inheritDoc
     */
    public function getUpdateDate()
    {
        return $this->updateDate;
    }

    /**
     * @inheritDoc
     */
    public function getContent()
    {

        $blocks = $this->page->getPageTemplateBlocks();
        $content = array();
        foreach($blocks as $block){
            if($this->locale == $block->getLocale()) $content[] = json_decode($block->getContent(), true);
        }
        return json_encode($content);
    }

    /**
     * @inheritDoc
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @inheritDoc
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * @inheritDoc
     */
    public function getSearchDescription()
    {
        return $this->searchDescription;
    }

    public function isIndexable(){

        if($this->page->getDeleted())
            return false;

        return true;
    }

    public function getIndexType(){
        return 'cms';
    }

    public function entityRender(){

        return array('title' => $this->title, 'description' => $this->metaDescription, 'url' => array('route' => '_cms_content', 'params' => array('id' => $this->page->getId(), 'page_parent_id' => is_null($this->page->getParent())? 0 : $this->page->getParent()->getId(), 'menu_id' => $this->page->getMenu()->getId(), 'lang' => $this->locale)));

    }

    public function entityRenderFront(){ return array('title' => $this->title, 'description' => $this->metaDescription, 'url' => array('route' => 'majes_page_'.$this->page->getId().'_'.$this->locale, 'params' => array()));}
    /**
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps()
    {
        $this->setUpdateDate(new \DateTime(date('Y-m-d H:i:s')));

        if($this->getCreateDate() == null)
        {
            $this->setCreateDate(new \DateTime(date('Y-m-d H:i:s')));
        }
    }

    /**
     * Gets the value of deleted.
     *
     * @return mixed
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Sets the value of deleted.
     *
     * @param mixed $deleted the deleted
     *
     * @return self
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     *
     * @ORM\PrePersist
     */
    public function defaultValues()
    {
        if(is_null($this->tags)){
            $this->tags='Page';
        }
    }
}
