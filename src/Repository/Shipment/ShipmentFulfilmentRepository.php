<?php

namespace App\Repository\Shipment;

use App\Entity\Shipment\ShipmentFulfilment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ShipmentFulfilment>
 *
 * @method ShipmentFulfilment|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShipmentFulfilment|null findOneBy(array $criteria, array $orderBy = null)
 * @method ShipmentFulfilment[]    findAll()
 * @method ShipmentFulfilment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShipmentFulfilmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShipmentFulfilment::class);
    }

//    /**
//     * @return ShipmentFulfilment[] Returns an array of ShipmentFulfilment objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ShipmentFulfilment
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
