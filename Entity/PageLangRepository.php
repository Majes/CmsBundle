<?php 
namespace Majes\CmsBundle\Entity;

use Doctrine\ORM\EntityRepository;

class PageLangRepository extends EntityRepository
{

    /**
     * GET all pages for a specific host and then generate menu
     */
    public function createIsActiveQueryBuilder() {

        $query = $this->createQueryBuilder('pl')
            ->innerJoin('pl.page', 'p')
            ->where('p.status != :status')
            ->setParameter('status', 'deleted')
            ->orderBy('p.id', 'ASC');

        return $query;
    }


    /**
     * GET last 5 updated pages
     */
    public function lastUpdated() {

        $query = $this->createQueryBuilder('pl')
            ->innerJoin('pl.page', 'p')
            ->where('p.status != :status')
            ->setParameter('status', 'deleted')
            ->orderBy('pl.updateDate', 'DESC')
            ->setFirstResult( 0 )
            ->setMaxResults( 5 )
            ->getQuery();

        return $query->getResult();
    }


}