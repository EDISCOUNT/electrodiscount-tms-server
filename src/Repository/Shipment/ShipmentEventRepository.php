<?php

namespace App\Repository\Shipment;

use App\Entity\Shipment\ShipmentEvent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ShipmentEvent>
 *
 * @method ShipmentEvent|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShipmentEvent|null findOneBy(array $criteria, array $orderBy = null)
 * @method ShipmentEvent[]    findAll()
 * @method ShipmentEvent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShipmentEventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShipmentEvent::class);
    }

//    /**
//     * @return ShipmentEvent[] Returns an array of ShipmentEvent objects
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

//    public function findOneBySomeField($value): ?ShipmentEvent
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
