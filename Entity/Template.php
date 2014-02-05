<?php
namespace Majes\CmsBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Majes\CoreBundle\Annotation\DataTable;


/**
 * Majes\CoreBundle\Entity\User\User
 *
 * @ORM\Table(name="cms_template")
 * @ORM\Entity
 */
class Template{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Majes\CoreBundle\Entity\User\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=150)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=50)
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
     * @ORM\OneToMany(targetEntity="TemplateBlock", mappedBy="template", cascade={"persist", "remove"})
     * @ORM\OrderBy({"sort" = "ASC"})
     */
    private $templateBlocks;
    
    /**
     * @ORM\OneToMany(targetEntity="Page", mappedBy="template", cascade={"persist", "remove"})
     * @ORM\OrderBy({"sort" = "ASC"})
     */
    private $templatePages;

    /**
     * @DataTable(isTranslatable=0, hasAdd=1, hasPreview=0, isDatatablejs=0)
     */
    public function __construct()
    {

        $datetime = new \DateTime();
        $this->createDate = $datetime;
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
    public function getTemplateBlocks()
    {
        //var_dump($this->blockAttributes); exit;
        return $this->templateBlocks->toArray();
    }

    /**
     * @inheritDoc
     */
    public function addTemplateBlock(\Majes\CmsBundle\Entity\TemplateBlock $templateBlock)
    {
        return $this->templateBlocks[] = $templateBlock;
    }

     public function removeTemplateBlock(\Majes\CmsBundle\Entity\TemplateBlock $templateBlock)
    {
        return $this->templateBlocks->removeElement($templateBlock);
    }

    public function removeTemplateBlocks()
    {
        foreach($this->templateBlocks as $templateBlock)
            $this->templateBlocks->removeElement($templateBlock);

        return;
    }


}