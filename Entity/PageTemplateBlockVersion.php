<?php
namespace Majes\CmsBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * Majes\CmsBundle\Entity\PageTemplateBlock
 *
 * @ORM\Table(name="cms_page_template_block_version")
 * @ORM\Entity
 */
class PageTemplateBlockVersion{
    /**
     * @ORM\ManyToOne(targetEntity="Majes\CmsBundle\Entity\PageTemplateBlock", inversedBy="versions")
     * @ORM\JoinColumn(name="page_template_block_id", referencedColumnName="id")
     * @ORM\Id
     */
    private $pageTemplateBlock;

    /**
     * @ORM\Column( type="integer")
     * @ORM\Id
     */
    private $version;


    /**
     * @ORM\ManyToOne(targetEntity="Majes\CoreBundle\Entity\User\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\Column(type="text")
     */
    private $locale;

    /**
     * @ORM\Column(type="string")
     */
    private $status;
    
    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="update_date", type="datetime")
     */
    private $updateDate;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="create_date", type="datetime")
     */
    private $createDate;

    /**
     * @inheritDoc
     */
    public function __construct()
    {

        $datetime = new \DateTime();
        $this->createDate = $datetime;
        $this->status = 'draft';


    }

    /**
     * @inheritDoc
     */
    public function setPageTemplateBlock(\Majes\CmsBundle\Entity\PageTemplateBlock $pageTemplateBlock)
    {
        $this->pageTemplateBlock = $pageTemplateBlock;
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
    public function setStatus($status)
    {
        $this->status = $status;
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
    public function getPageTemplateBlock()
    {
        return $this->pageTemplateBlock;
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
    public function getStatus()
    {
        return $this->status;
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


}