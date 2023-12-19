<?php

namespace App\Repository\Shipment;

use App\Entity\Shipment\ShipmentItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ShipmentItem>
 *
 * @method ShipmentItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShipmentItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method ShipmentItem[]    findAll()
 * @method ShipmentItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShipmentItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShipmentItem::class);
    }

//    /**
//     * @return ShipmentItem[] Returns an array of ShipmentItem objects
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

//    public function findOneBySomeField($value): ?ShipmentItem
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
