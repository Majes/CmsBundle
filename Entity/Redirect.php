<?php
namespace Majes\CmsBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Cmf\Component\Routing\RouteObjectInterface;
use Symfony\Component\Routing\Route as SymfonyRoute;
use Doctrine\ORM\Mapping as ORM;
use Majes\CoreBundle\Annotation\DataTable;
use Majes\CoreBundle\Entity\Host;

/**
 * Majes\CmsBundle\Entity\Redirect
 *
 * @ORM\Entity(repositoryClass="Majes\CmsBundle\Entity\RedirectRepository")
 * @ORM\Table(name="cms_redirect")
 * @ORM\HasLifeCycleCallbacks
 */
class Redirect extends SymfonyRoute implements RouteObjectInterface{
    /**
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="host", type="string", length=255, nullable=false)
     */
    private $host;

    /**
     * @ORM\Column(name="url", type="string", length=255, nullable=false)
     */
    private $url;

    /**
     * @ORM\Column(name="redirect_url", type="string", length=255, nullable=false)
     */
    private $redirectUrl;

    /**
     * @ORM\Column(name="type", type="boolean", nullable=false)
     */
    private $permanent;

    protected $content;

    /**
     * @DataTable(isTranslatable=0, hasAdd=1, hasPreview=0, isDatatablejs=1, ajaxUrl="_admin_redirects")
     */
    public function __construct($addFormatPattern = false){
        $this->setDefaults(array());
        $this->setRequirements(array());
        $this->setOptions(array());

        $this->addFormatPattern = $addFormatPattern;
        if ($this->addFormatPattern) {
            $this->setDefault('_format', 'html');
            $this->setRequirement('_format', 'html');
        }
    }

    /**
     * @inheritDoc
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setPermanent($permanent)
    {
        $this->permanent = $permanent;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setRedirectUrl($redirectUrl)
    {
        $this->redirectUrl = $redirectUrl;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setHost($host)
    {
        if($host instanceof Host)
            $this->host = $host->getUrl();
        else
            $this->host = $host;
        return $this;
    }
    
    /**
     * @DataTable(label="Id", column="id", isSortable=1)
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @DataTable(label="Url", column="url", isSortable=1)
     */
    public function getUrl()
    {
        return $this->url;
    }
    
    /**
     * @DataTable(label="Redirect Url", column="redirectUrl", isSortable=1)
     */
    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }
    
    /**
     * @DataTable(label="Permanent", column="permanent", isSortable=1)
     */
    public function getPermanent()
    {
        return $this->permanent;
    }

    /**
     * @inheritDoc
     */
    public function isPermanent()
    {
        return $this->permanent;
    }

    /**
     * @DataTable(label="Host", column="host", isSortable=1)
     */
    public function getHost()
    {
        return $this->host;
    }
    
    public function getContent(){
        return $this->content;
    }
    
    public function getRouteKey(){ return null;}

}
