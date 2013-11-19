<?php
namespace Majes\CmsBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Majes\CoreBundle\Annotation\DataTable;


/**
 * Majes\CmsBundle\Entity\Page
 *
 * @ORM\Entity(repositoryClass="Majes\CmsBundle\Entity\PageRepository")
 * @ORM\Table(name="cms_page")
 */
class Page{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="link_url", type="string", length=255)
     */
    private $linkUrl;

    /**
     * @ORM\Column(name="target_url", type="string", length=255)
     */
    private $targetUrl;

    /**
     * @ORM\Column(name="sort", type="integer")
     */
    private $sort;

    /**
     * @ORM\Column(name="is_inmenu", type="boolean")
     */
    private $isInmenu;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(name="is_folder", type="boolean")
     */
    private $isFolder;

    /**
     * @ORM\Column(name="enable_comments", type="boolean")
     */
    private $enableComments;
    
    /**
     * @ORM\Column(name="status", type="string")
     */
    private $status;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="create_date", type="datetime")
     */
    private $createDate;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="update_date", type="datetime")
     */
    private $updateDate;

    /**
     * @ORM\ManyToOne(targetEntity="Majes\CoreBundle\Entity\User\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Majes\CmsBundle\Entity\Menu")
     * @ORM\JoinColumn(name="menu_id", referencedColumnName="id")
     */
    private $menu;

    /**
     * @ORM\ManyToOne(targetEntity="Majes\CmsBundle\Entity\Page")
     * @ORM\JoinColumn(name="page_id_parent", referencedColumnName="id")
     */
    private $parent;

    /**
     * @ORM\ManyToOne(targetEntity="Majes\CmsBundle\Entity\Host")
     * @ORM\JoinColumn(name="host_id", referencedColumnName="id")
     */
    private $host;

    /**
     * @ORM\ManyToOne(targetEntity="Majes\CmsBundle\Entity\Template")
     * @ORM\JoinColumn(name="template_id", referencedColumnName="id")
     */
    private $template;

    /**
     * @ORM\OneToMany(targetEntity="Majes\CmsBundle\Entity\PageLang", mappedBy="page", cascade={"persist"})
     * @ORM\JoinColumn(name="id", referencedColumnName="page_id")
     */
    private $langs;

    /**
     * Current lang
     */
    private $lang = null;

    /**
     * @ORM\OneToMany(targetEntity="PageTemplateBlock", mappedBy="page", cascade={"persist"})
     */
    private $pageTemplateBlocks;

    /**
     * @ORM\ManyToMany(targetEntity="Majes\CoreBundle\Entity\User\Role")
     * @ORM\JoinTable(name="cms_page_role",
     *      joinColumns={@ORM\JoinColumn(name="page_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     * )
     */
    private $roles;

    /**
     * @inheritDoc
     */
    public function __construct(){
        $this->createDate = new \DateTime();
        $this->langs = new \Doctrine\Common\Collections\ArrayCollection();
        $this->sort = 0;
        $this->status = '';

        $this->pageTemplateBlocks = new \Doctrine\Common\Collections\ArrayCollection();
        $this->roles = new \Doctrine\Common\Collections\ArrayCollection();
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
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setEnableComments($enableComments)
    {
        $this->enableComments = $enableComments;
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
    public function setHost(\Majes\CmsBundle\Entity\Host $host)
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
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @inheritDoc
     */
    public function getEnableComments()
    {
        return $this->enableComments;
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
    

}