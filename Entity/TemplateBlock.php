<?php
namespace Majes\CmsBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;


/**
 * Majes\CmsBundle\Entity\TemplateBlock
 *
 * @ORM\Table(name="cms_template_block")
 * @ORM\Entity
 */
class TemplateBlock{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Majes\CmsBundle\Entity\Block", inversedBy="templateBlocks", cascade={"persist"})
     * @ORM\JoinColumn(name="block_id", referencedColumnName="id")
     */
    private $block;

    /**
     * @ORM\ManyToOne(targetEntity="Majes\CmsBundle\Entity\Template", inversedBy="templateBlocks", cascade={"persist"})
     * @ORM\JoinColumn(name="template_id", referencedColumnName="id")
     */
    private $template;

    /**
     * @ORM\Column(type="integer")
     */
    private $sort;

    /**
     * @ORM\Column(type="boolean", name="is_mobile")
     */
    private $isMobile;

    /**
     * @ORM\Column(type="boolean", name="is_tablet")
     */
    private $isTablet;

    /**
     * @ORM\Column(type="boolean", name="is_desktop")
     */
    private $isDesktop;

    /**
     * @ORM\Column(type="boolean", name="is_repeatable")
     */
    private $isRepeatable;
        
    /**
     * @ORM\Column(type="string", length=100)
     */
    private $ref;

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
        $this->isMobile = 1;
        $this->isTablet = 1;
        $this->isDesktop = 1;

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
    public function setBlock(\Majes\CmsBundle\Entity\Block $block)
    {
        $this->block = $block;
        $this->setIsRepeatable($block->getIsRepeatable());
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setTemplate(\Majes\CmsBundle\Entity\Template $template)
    {
        $this->template = $template;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setSort($sort)
    {
        $this->sort = $sort;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setIsMobile($isMobile)
    {
        $this->isMobile = $isMobile;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setIsTablet($isTablet)
    {
        $this->isTablet = $isTablet;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setIsDesktop($isDesktop)
    {
        $this->isDesktop = $isDesktop;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setIsRepeatable($isRepeatable)
    {
        $this->isRepeatable = $isRepeatable;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setRef($ref)
    {
        $this->ref = $ref;
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
    public function getBlock()
    {
        return $this->block;
    }

    /**
     * @inheritDoc
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @inheritDoc
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * @inheritDoc
     */
    public function getIsMobile()
    {
        return $this->isMobile;
    }

    /**
     * @inheritDoc
     */
    public function getIsTablet()
    {
        return $this->isTablet;
    }

    /**
     * @inheritDoc
     */
    public function getIsDesktop()
    {
        return $this->isDesktop;
    }

    /**
     * @inheritDoc
     */
    public function getIsRepeatable()
    {
        return $this->isRepeatable;
    }

    /**
     * @inheritDoc
     */
    public function getRef()
    {
        return $this->ref;
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