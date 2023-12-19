<?php

namespace App\Repository\Order;

use App\Entity\Order\AdditionalService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AdditionalService>
 *
 * @method AdditionalService|null find($id, $lockMode = null, $lockVersion = null)
 * @method AdditionalService|null findOneBy(array $criteria, array $orderBy = null)
 * @method AdditionalService[]    findAll()
 * @method AdditionalService[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdditionalServiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdditionalService::class);
    }

//    /**
//     * @return AdditionalService[] Returns an array of AdditionalService objects
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

//    public function findOneBySomeField($value): ?AdditionalService
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
