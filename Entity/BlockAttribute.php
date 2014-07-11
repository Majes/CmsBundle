<?php
namespace Majes\CmsBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Majes\CoreBundle\Annotation\DataTable;


/**
 * Majes\CmsBundle\Entity\BlockAttribute
 * @ORM\Entity
 * @ORM\Table(name="cms_block_attribute")
 * @ORM\HasLifecycleCallbacks
 */
class BlockAttribute{
    /**
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Majes\CmsBundle\Entity\Block", inversedBy="blockAttributes", cascade={"persist"})
     * @ORM\JoinColumn(name="block_id", referencedColumnName="id", nullable=false)
     */
    private $block;

    /**
     * @ORM\ManyToOne(targetEntity="Majes\CmsBundle\Entity\Attribute", inversedBy="blockAttributes", cascade={"persist"})
     * @ORM\JoinColumn(name="attribute_id", referencedColumnName="id", nullable=false)
     */
    private $attribute;

    /**
     * @ORM\Column(name="sort", type="integer", nullable=false)
     */
    private $sort=0;
    
    /**
     * @ORM\Column(name="ref", type="string", length=150, nullable=false)
     */
    private $ref='';

    /**
     * @ORM\Column(name="title", type="string", length=150, nullable=true)
     */
    private $title=null;

    /**
     * @ORM\Column(name="update_date", type="datetime", nullable=false)
     */
    private $updateDate;

    /**
     * @ORM\Column(name="create_date", type="datetime", nullable=false)
     */
    private $createDate;

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
    public function setBlock(\Majes\CmsBundle\Entity\Block $block)
    {
        $this->block = $block;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setAttribute(\Majes\CmsBundle\Entity\Attribute $attribute)
    {
        $this->attribute = $attribute;
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
    public function setTitle($title)
    {
        $this->title = $title;
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
    public function getAttribute()
    {
        return $this->attribute;
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
    public function getRef()
    {
        return $this->ref;
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
    public function getCreateDate()
    {
        return $this->createDate;
    }

    /**
     * @inheritDoc
     * @DataTable(label="Last Upd.", column="updateDate", isSortable=1)
     */
    public function getUpdateDate()
    {
        return $this->updateDate;
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