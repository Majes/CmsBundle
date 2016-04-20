<?php
namespace Majes\CmsBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Majes\CoreBundle\Annotation\DataTable;


/**
 * Majes\CmsBundle\Entity\Page
 *
 * @ORM\Entity(repositoryClass="Majes\CmsBundle\Entity\PageRepository")
 * @ORM\Table(name="cms_page", indexes={
 *      @ORM\Index(name="is_inmenu", columns={"is_inmenu"}),
 *      @ORM\Index(name="is_active", columns={"is_active"})
 * })
 * @ORM\HasLifecycleCallbacks
 */
class Page{
    /**
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="link_url", type="string", length=255, nullable=true)
     */
    private $linkUrl=null;

    /**
     * @ORM\Column(name="target_url", type="string", length=6, nullable=false)
     */
    private $targetUrl='_self';

    /**
     * @ORM\Column(name="sort", type="integer", nullable=false)
     */
    private $sort=0;

    /**
     * @ORM\Column(name="is_inmenu", type="boolean", nullable=false)
     */
    private $isInmenu=1;

    /**
     * @ORM\Column(name="display_menu", type="boolean", nullable=false)
     */
    private $displayMenu;

    /**
     * @ORM\Column(name="is_active", type="boolean", nullable=false)
     */
    private $isActive=1;

    /**
     * @ORM\Column(name="is_folder", type="boolean", nullable=false)
     */
    private $isFolder=0;

    /**
     * @ORM\Column(name="has_option", type="boolean", nullable=false)
     */
    private $hasOption=0;

    /**
     * @ORM\Column(name="deleted", type="boolean", nullable=false)
     */
    private $deleted=0;

    /**
     * @ORM\Column(name="create_date", type="datetime", nullable=false)
     */
    private $createDate;

    /**
     * @ORM\Column(name="update_date", type="datetime", nullable=false)
     */
    private $updateDate;

    /**
     * @ORM\ManyToOne(targetEntity="Majes\CoreBundle\Entity\User\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Majes\CmsBundle\Entity\Menu", fetch="EAGER")
     * @ORM\JoinColumn(name="menu_id", referencedColumnName="id", nullable=true)
     */
    private $menu=null;

    /**
     * @ORM\ManyToOne(targetEntity="Majes\CmsBundle\Entity\Page", fetch="EAGER")
     * @ORM\JoinColumn(name="page_id_parent", referencedColumnName="id", nullable=true)
     */
    private $parent=null;

    /**
     * @ORM\ManyToOne(targetEntity="Majes\CoreBundle\Entity\Host", fetch="EAGER")
     * @ORM\JoinColumn(name="host_id", referencedColumnName="id", nullable=false)
     */
    private $host;

    /**
     * @ORM\ManyToOne(targetEntity="Majes\CmsBundle\Entity\Template", inversedBy="templatePages", fetch="EAGER")
     * @ORM\JoinColumn(name="template_id", referencedColumnName="id", nullable=false)
     */
    private $template;

    /**
     * @ORM\OneToMany(targetEntity="Majes\CmsBundle\Entity\PageLang", mappedBy="page", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="id", referencedColumnName="page_id")
     */
    private $langs;

    /**
     * Current lang
     */
    private $lang = null;

    /**
     * @ORM\OneToMany(targetEntity="PageTemplateBlock", mappedBy="page", cascade={"persist", "remove"})
     */
    private $pageTemplateBlocks;

    /**
     * @ORM\ManyToMany(targetEntity="Majes\CoreBundle\Entity\User\Role", inversedBy="pages", cascade={"remove"})
     * @ORM\JoinTable(name="cms_page_role",
     *      joinColumns={@ORM\JoinColumn(name="page_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     * )
     */
    private $roles;

    /**
     * @ORM\Column(name="icon", type="string", length=50, nullable=true)
     */
    private $icon;

    /**
     * @inheritDoc
     */
    public function __construct(){
        $this->createDate = new \DateTime();
        $this->langs = new \Doctrine\Common\Collections\ArrayCollection();
        $this->sort = 0;

        $this->pageTemplateBlocks = new \Doctrine\Common\Collections\ArrayCollection();
        $this->roles = new \Doctrine\Common\Collections\ArrayCollection();

        $this->displayMenu = true;
        $this->isFolder = false;
        $this->isInmenu = true;
        $this->hasOption = false;
        $this->isActive = true;
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
    public function setLinkUrl($linkUrl)
    {
        $this->linkUrl = $linkUrl;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setTargetUrl($targetUrl)
    {
        $this->targetUrl = $targetUrl;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;
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
    public function setIsInmenu($isInmenu)
    {
        $this->isInmenu = $isInmenu;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setDisplayMenu($displayMenu)
    {
        $this->displayMenu = $displayMenu;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setIsFolder($isFolder)
    {
        $this->isFolder = $isFolder;
        return $this;
    }


    /**
     * @inheritDoc
     */
    public function setHasOption($hasOption)
    {
        $this->hasOption = $hasOption;
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
    public function setUser(\Majes\CoreBundle\Entity\User\User $user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setMenu(\Majes\CmsBundle\Entity\Menu $menu = null)
    {
        $this->menu = $menu;
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
    public function setHost(\Majes\CoreBundle\Entity\Host $host)
    {
        $this->host = $host;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setParent(\Majes\CmsBundle\Entity\Page $parent = null)
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setLang($locale)
    {
        if(is_null($this->langs)){
            $this->lang = null;
            return $this;
        }

        foreach ($this->langs as $lang) {
            if($lang->getLocale() == $locale){
                $this->lang = $lang;
                break;
            }
        }
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
    public function getLinkUrl()
    {
        return $this->linkUrl;
    }

    /**
     * @inheritDoc
     */
    public function getTargetUrl()
    {
        return $this->targetUrl;
    }

    /**
     * @inheritDoc
     */
    public function getIcon()
    {
        return $this->icon;
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
    public function getIsInmenu()
    {
        return $this->isInmenu;
    }

    /**
     * @inheritDoc
     */
    public function getDisplayMenu()
    {
        return $this->displayMenu;
    }

    /**
     * @inheritDoc
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * @inheritDoc
     */
    public function getIsFolder()
    {
        return $this->isFolder;
    }


    /**
     * @inheritDoc
     */
    public function getHasOption()
    {
        return $this->hasOption;
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
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @inheritDoc
     */
    public function getMenu()
    {
        return $this->menu;
    }

    /**
     * @inheritDoc
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @inheritDoc
     */
    public function getHost()
    {
        return $this->host;
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
    public function getLang()
    {

        return $this->lang;
    }

    /**
     * @inheritDoc
     */
    public function getLangs()
    {
        return $this->langs->toArray();
    }

    /**
     * @inheritDoc
     */
    public function getPageTemplateBlocks()
    {
        //var_dump($this->blockAttributes); exit;
        return $this->pageTemplateBlocks->toArray();
    }

    /**
     * @inheritDoc
     */
    public function getRoles()
    {
        return $this->roles->toArray();
    }

    /**
     * @inheritDoc
     */
    public function addPageTemplateBlock(\Majes\CmsBundle\Entity\PageTemplateBlock $pageTemplateBlock)
    {
        return $this->pageTemplateBlocks[] = $pageTemplateBlock;
    }

    /**
     * @inheritDoc
     */
    public function addLang(\Majes\CmsBundle\Entity\PageLang $lang)
    {
        return $this->langs[] = $lang;
    }

    public function hasLang($locale){
        $langs = $this->getLangs();

        $roles_array = array();
        foreach($langs as $lang){
            if($locale == $lang->getLocale()) return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function addRole(\Majes\CoreBundle\Entity\User\Role $role)
    {
        return $this->roles[] = $role;
    }

    public function hasRole($role_id){
        $roles = $this->getRoles();

        $roles_array = array();
        foreach($roles as $role){
            $roles_array[] = $role->getId();
        }

        if(in_array($role_id, $roles_array)) return true;
        return false;
    }

    public function removeRole(\Majes\CoreBundle\Entity\User\Role $role)
    {
        return $this->roles->removeElement($role);
    }

    public function removeRoles()
    {
        foreach($this->roles as $role)
            $this->roles->removeElement($role);

        return;
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

    /**
     *
     * @ORM\PrePersist
     */
    public function defaultValues()
    {
        if(is_null($this->targetUrl)){
            $this->targetUrl='_self';
        }
    }
}
