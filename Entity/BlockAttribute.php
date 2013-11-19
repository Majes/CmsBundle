<?php
namespace Majes\CmsBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;


/**
 * Majes\CmsBundle\Entity\BlockAttribute
 *
 * @ORM\Table(name="cms_block_attribute")
 * @ORM\Entity
 */
class BlockAttribute{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Majes\CmsBundle\Entity\Block", inversedBy="blockAttributes", cascade={"persist"})
     * @ORM\JoinColumn(name="block_id", referencedColumnName="id")
     */
    private $block;

    /**
     * @ORM\ManyToOne(targetEntity="Majes\CmsBundle\Entity\Attribute", inversedBy="blockAttributes", cascade={"persist"})
     * @ORM\JoinColumn(name="attribute_id", referencedColumnName="id")
     */
    private $attribute;

    /**
     * @ORM\Column(type="integer")
     */
    private $sort;
    
    /**
     * @ORM\Column(type="string", length=150)
     */
    private $ref;

    /**
     * @ORM\Column(type="string", length=150)
     */
    private $title;

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


}