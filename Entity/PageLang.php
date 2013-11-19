<?php
namespace Majes\CmsBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Majes\CoreBundle\Annotation\DataTable;


/**
 * Majes\CmsBundle\Entity\PageLang
 *
 * @ORM\Table(name="cms_page_lang")
 * @ORM\Entity
 */
class PageLang{


    /**
     * @ORM\ManyToOne(targetEntity="Majes\CmsBundle\Entity\Page", inversedBy="langs", cascade={"persist"})
     * @ORM\JoinColumn(name="page_id", referencedColumnName="id")
     * @ORM\Id
     */
    private $page;

    /**
     * @ORM\Column( type="string", length=5)
     * @ORM\Id
     */
    private $locale;

    /**
     * @ORM\ManyToOne(targetEntity="Majes\CoreBundle\Entity\User\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=150)
     */
    private $url;

    /**
     * @ORM\Column(name="url_root", type="string", length=255)
     */
    private $urlRoot;

    /**
     * @ORM\Column(name="meta_description", type="string", length=255)
     */
    private $metaDescription;

    /**
     * @ORM\Column(name="meta_keywords", type="string", length=255)
     */
    private $metaKeywords;

    /**
     * @ORM\Column(name="meta_title", type="string", length=150)
     */
    private $metaTitle;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="create_date", type="datetime")
     */
    private $createDate;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="update_date", type="datetime")
     */
    private $updateDate;


    /**
     * @inheritDoc
     */
    public function __construct(){
        $this->createDate = new \DateTime();
        $this->url_root = '';
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
    

}