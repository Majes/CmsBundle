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
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="ref", type="string", length=100, nullable=false)
     */
    private $ref;

    /**
     * @ORM\Column(name="title", type="string", length=150, nullable=false)
     */
    private $title;
    

    /**
     * @ORM\OneToMany(targetEntity="BlockAttribute", mappedBy="attribute")
     */
    private $blockAttributes;

    /**
     * @ORM\Column(name="setup", type="boolean", nullable=false)
     */
    private $setup=false;

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

    /**
     * Gets the value of setup.
     *
     * @return mixed
     */
    public function getSetup()
    {
        return $this->setup;
    }

    /**
     * Sets the value of setup.
     *
     * @param mixed $setup the setup
     *
     * @return self
     */
    public function setSetup($setup)
    {
        $this->setup = $setup;

        return $this;
    }
}