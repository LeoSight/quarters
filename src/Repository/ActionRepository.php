<?php

namespace App\Repository;

use App\Entity\Action;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Action>
 *
 * @method Action|null find($id, $lockMode = null, $lockVersion = null)
 * @method Action|null findOneBy(array $criteria, array $orderBy = null)
 * @method Action[]    findAll()
 * @method Action[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Action::class);
    }

    public function save(Action $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Action $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Action[] Returns an array of Action objects
     */
    public function findActionsToBeRan(): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.status = 0')
            ->andWhere('a.runTime < :now')
            ->setParameter('now', new \DateTime())
            ->orderBy('a.runTime', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Action[] Returns an array of Action objects
     */
    public function findOldActions(): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.status = 1')
            ->andWhere('a.runTime < :old')
            ->setParameter('old', new \DateTime('-2 days'))
            ->orderBy('a.runTime', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findUserCurrentAction(User $user): Action|null
    {
        return $this->findOneBy(['user' => $user, 'status' => 0]);
    }

//    /**
//     * @return Action[] Returns an array of Action objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Action
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
