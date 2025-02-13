<?php

namespace App\Repository;

use App\Entity\Activity\Activity;
use App\Entity\Group\Group;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Activity>
 *
 * @method Activity|null find($id, $lockMode = null, $lockVersion = null)
 * @method Activity|null findOneBy(array $criteria, array $orderBy = null)
 * @method Activity[]    findAll()
 * @method Activity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActivityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Activity::class);
    }

    /**
     * @param Group[] $groups
     *
     * @return Activity[] Returns an array of Activity objects
     */
    public function findAuthor($groups)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('(p.author IN (:groups))')
            ->setParameter('groups', $groups)
            ->orderBy('p.start', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Activity[] Returns an array of Activity objects
     */
    public function findUpcoming()
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.end > CURRENT_TIMESTAMP()')
            ->orderBy('p.start', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param Group[] $groups
     *
     * @return Activity[] Returns an array of Activity objects
     */
    public function findUpcomingByGroup($groups)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.end > CURRENT_TIMESTAMP()')
            ->andWhere('(p.target IN (:groups)) OR (p.target is NULL)')
            ->setParameter('groups', $groups)
            ->orderBy('p.start', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param Group[] $groups
     *
     * @return Activity[] Returns an array of Activity objects
     */
    public function findVisibleUpcomingByGroup($groups)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.end > CURRENT_TIMESTAMP()')
            ->andWhere('(p.target IN (:groups)) OR (p.target is NULL)')
            ->andWhere('p.visibleAfter IS NOT NULL')
            ->andWhere('p.visibleAfter < CURRENT_TIMESTAMP()')
            ->setParameter('groups', $groups)
            ->orderBy('p.start', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    // /**
    //  * @return Activity[] Returns an array of Activity objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Activity
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
