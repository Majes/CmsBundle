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


}