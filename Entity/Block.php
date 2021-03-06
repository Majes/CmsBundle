<?php
namespace Majes\CmsBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Majes\CoreBundle\Annotation\DataTable;


/**
 * Majes\CmsBundle\Entity\Block
 *
 * @ORM\Entity
 * @ORM\Table(name="cms_block")
 * @ORM\HasLifecycleCallbacks
 */
class Block{
    /**
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Majes\CoreBundle\Entity\User\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(name="is_repeatable", type="boolean", nullable=false)
     */
    private $isRepeatable=0;

    /**
     * @ORM\Column(name="title", type="string", length=150, nullable=false)
     */
    private $title;

    /**
     * @ORM\Column(name="ref", type="string", length=50, nullable=false)
     */
    private $ref='';
    
    /**
     * @ORM\Column(name="update_date", type="datetime", nullable=false)
     */
    private $updateDate;

    /**
     * @ORM\Column(name="create_date", type="datetime", nullable=false)
     */
    private $createDate;

    /**
     * @ORM\OneToMany(targetEntity="BlockAttribute", mappedBy="block", cascade={"persist", "remove"})
     * @ORM\OrderBy({"sort" = "ASC"})
     */
    private $blockAttributes;

    /**
     * @ORM\OneToMany(targetEntity="TemplateBlock", mappedBy="block")
     */
    private $templateBlocks;

    /**
     * @ORM\Column(name="deleted", type="boolean", nullable=false)
     */
    private $deleted=0;

    /**
     * @DataTable(isTranslatable=0, hasAdd=1, hasPreview=0, isDatatablejs=0)
     */
    public function __construct()
    {

        $datetime = new \DateTime();
        $this->createDate = $datetime;
        $this->blockAttributes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->templateBlocks = new \Doctrine\Common\Collections\ArrayCollection();

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
    public function setIsRepeatable($isRepeatable)
    {
        $this->isRepeatable = $isRepeatable;
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
     * @DataTable(label="Id", column="id", isSortable=1)
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     * @DataTable(label="Is repeatable", column="isRepeatable", isSortable=1)
     */
    public function getIsRepeatable()
    {
        return $this->isRepeatable;
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
     * @DataTable(label="Title", column="title", isSortable=1)
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @inheritDoc
     * @DataTable(label="Ref", column="ref", isSortable=0)
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
     * @DataTable(label="Last Upd.", column="updateDate", isSortable=1, format="datetime")
     */
    public function getUpdateDate()
    {
        return $this->updateDate;
    }

    /**
     * @inheritDoc
     */
    public function getBlockAttributes()
    {
        //var_dump($this->blockAttributes); exit;
        return $this->blockAttributes->toArray();
    }

    /**
     * @inheritDoc
     */
    public function addBlockAttribute(\Majes\CmsBundle\Entity\BlockAttribute $blockAttribute)
    {
        return $this->blockAttributes[] = $blockAttribute;
    }

    public function hasAttribute($attribute_id){
        $attributes = $this->getBlockAttributes();

        $attributes_array = array();
        foreach($attributes as $attribute){
            $attributes_array[] = $attribute->getId();
        }

        if(in_array($attribute_id, $attributes_array)) return true;
        return false;
    }

    public function removeBlockAttribute(\Majes\CmsBundle\Entity\BlockAttribute $blockAttribute)
    {
        return $this->blockAttributes->removeElement($blockAttribute);
    }

    public function removeBlockAttributes()
    {
        foreach($this->blockAttributes as $attribute)
            $this->blockAttributes->removeElement($attribute);

        return;
    }


    /**
     * @inheritDoc
     */
    public function getTemplateBlock()
    {
        //var_dump($this->blockAttributes); exit;
        return $this->templateBlocks->toArray();
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

}