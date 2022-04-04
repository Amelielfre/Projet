<?php

namespace App\Repository;

use App\Entity\ArchivesSorties;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ArchivesSorties|null find($id, $lockMode = null, $lockVersion = null)
 * @method ArchivesSorties|null findOneBy(array $criteria, array $orderBy = null)
 * @method ArchivesSorties[]    findAll()
 * @method ArchivesSorties[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArchivesSortiesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ArchivesSorties::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(ArchivesSorties $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(ArchivesSorties $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return ArchivesSorties[] Returns an array of ArchivesSorties objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ArchivesSorties
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
