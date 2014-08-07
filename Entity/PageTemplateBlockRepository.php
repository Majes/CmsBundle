<?php 
namespace Majes\CmsBundle\Entity;

use Doctrine\ORM\EntityRepository;

class PageTemplateBlockRepository extends EntityRepository
{

    public function findByContentLike($term, $limit=null) {

        $qb = $this->createQueryBuilder('a');
        $qb ->select('a')
            ->add('where', $qb->expr()->like('a.content', ':term'))
            ->setParameter('term', '%' . $term . '%');

        if( $limit != null)
        {
            $qb->setMaxResults( $limit );
        }

        return $qb->getQuery()->getResult();
    }


}