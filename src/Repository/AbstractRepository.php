<?php


namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;


/**
 * Class AbstractRepository
 * @package App\Repository
 */
abstract class AbstractRepository extends ServiceEntityRepository
{

    /**
     * @param QueryBuilder $qb
     * @param int $limit
     * @param int $offset
     * @return Pagerfanta
     */
    protected function paginate(QueryBuilder $qb, int $limit = 20, int $offset = 0)
    {
        if (0 == $limit) {
            throw new \LogicException('limit must be greater than 0.');
        }

       $pager = new Pagerfanta(new DoctrineORMAdapter($qb));
       $currentPage = ceil(($offset + 1) / $limit);
       $pager->setCurrentPage($currentPage);
       $pager->setMaxPerPage($limit);

       return $pager;
    }
}