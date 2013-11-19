<?php
namespace Majes\CmsBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Majes\CoreBundle\Annotation\DataTable;


/**
 * Majes\CmsBundle\Entity\Block
 *
 * @ORM\Table(name="cms_attribute")
 * @ORM\Entity
 */
class Attribute{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $ref;

    /**
     * @ORM\Column(type="string", length=150)
     */
    private $title;
    

    /**
     * @ORM\OneToMany(targetEntity="BlockAttribute", mappedBy="attribute")
     */
    private $blockAttributes;

    /**
     * @DataTable(isTranslatable=0, hasAdd=1, hasPreview=0, isDatatablejs=0)
     */
    public function __construct(){
        $this->blockAttributes = new \Doctrine\Common\Collections\ArrayCollection();

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
    public function setRef($ref)
    {
        $this->ref = $ref;
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
     * @DataTable(label="Id", column="id", isSortable=1)
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function getBlockAttributes()
    {
        return $this->blockAttributes->toArray();
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
     * @DataTable(label="Title", column="title", isSortable=1)
     */
    public function getTitle()
    {
        return $this->title;
    }



}