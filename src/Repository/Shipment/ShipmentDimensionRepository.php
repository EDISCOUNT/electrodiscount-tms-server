<?php

namespace App\Repository\Shipment;

use App\Entity\Shipment\ShipmentDimension;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ShipmentDimension>
 *
 * @method ShipmentDimension|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShipmentDimension|null findOneBy(array $criteria, array $orderBy = null)
 * @method ShipmentDimension[]    findAll()
 * @method ShipmentDimension[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShipmentDimensionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShipmentDimension::class);
    }

//    /**
//     * @return ShipmentDimension[] Returns an array of ShipmentDimension objects
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

//    public function findOneBySomeField($value): ?ShipmentDimension
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
