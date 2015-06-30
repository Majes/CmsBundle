<?php 
namespace Majes\CmsBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Majes\CmsBundle\Entity\Route;
use Doctrine\ORM\Tools\Pagination\Paginator;

class RedirectRepository extends EntityRepository
{

	public function findForAdmin($offset = 0, $limit = 10, $search = ''){
        $qb = $this->createQueryBuilder('s');

        if(!empty($search))
            $qb->where('s.url like :search or s.redirectUrl like :search or s.host like :search')
                ->setParameter('search', '%'.$search.'%');

        $qb
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        $paginator = new Paginator($qb, $fetchJoinCollection = true);

        return $paginator;
    }

}
