<?php 
namespace Majes\CmsBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Majes\CmsBundle\Entity\Route;

class PageRepository extends EntityRepository
{

    private $_menu;
    private $_pages;

    public function findAllOrdered()
    {
        return $this->findBy(array(), array('sort' => 'ASC'));
    }

}
