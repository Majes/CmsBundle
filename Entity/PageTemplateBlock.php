<?php
namespace Majes\CmsBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * Majes\CmsBundle\Entity\PageTemplateBlock
 *
 * @ORM\Table(name="cms_page_template_block", indexes={
 *      @ORM\Index(name="locale", columns={"locale"})
 * })
 * @ORM\Entity(repositoryClass="Majes\CmsBundle\Entity\PageTemplateBlockRepository")
 * @ORM\HasLifecycleCallbacks
 */
class PageTemplateBlock{
    /**
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Majes\CmsBundle\Entity\TemplateBlock")
     * @ORM\JoinColumn(name="template_block_id", referencedColumnName="id", nullable=false)
     */
    private $templateBlock;

    /**
     * @ORM\ManyToOne(targetEntity="Majes\CmsBundle\Entity\Page", inversedBy="pageTemplateBlocks", cascade={"persist"})
     * @ORM\JoinColumn(name="page_id", referencedColumnName="id", nullable=false)
     */
    private $page;

    /**
     * @ORM\ManyToOne(targetEntity="Majes\CoreBundle\Entity\User\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(name="locale", type="string", length=5, nullable=false)
     */
    private $locale;

    /**
     * @ORM\Column(name="version", type="integer", nullable=false)
     */
    private $version=1;

    /**
     * @ORM\Column(name="content", type="text", nullable=false)
     */
    private $content;

    /**
     * @ORM\Column(name="update_date", type="datetime", nullable=false)
     */
    private $updateDate;

    /**
     * @ORM\Column(name="create_date", type="datetime", nullable=false)
     */
    private $createDate;

    /**
     * @ORM\OneToMany(targetEntity="Majes\CmsBundle\Entity\PageTemplateBlockVersion", mappedBy="pageTemplateBlock", cascade={"persist", "remove"})
     */
    private $versions;

    private $lastVersion;

    /**
     * @inheritDoc
     */
    public function __construct()
    {

        $datetime = new \DateTime();
        $this->createDate = $datetime;

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
    public function setTemplateBlock(\Majes\CmsBundle\Entity\TemplateBlock $templateBlock)
    {
        $this->templateBlock = $templateBlock;
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
    public function setVersion($version)
    {
        $this->version = $version;
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
    public function setLocale($locale)
    {
        $this->locale = $locale;
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
    public function getId()
    {
        return $this->id;
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
    public function getTemplateBlock()
    {
        return $this->templateBlock;
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
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @inheritDoc
     */
    public function getContent()
    {
        return $this->content;
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
    public function getVersions()
    {
        return $this->versions;
    }

    /**
     * @inheritDoc
     */
    public function getDraft()
    {
        foreach($this->versions as $version){

            if($version->getStatus() == 'draft')
                return $version;

        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function getLastVersion()
    {
        $version_num = $this->version;
        foreach($this->versions as $version){

            if($version->getVersion() > $version_num)
                $version_num = $version->getVersion();

        }

        return $version_num;
    }
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
}