<?php

namespace App\Repository\Shipment;

use App\Entity\Shipment\ShipmentAttachment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ShipmentAttachment>
 *
 * @method ShipmentAttachment|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShipmentAttachment|null findOneBy(array $criteria, array $orderBy = null)
 * @method ShipmentAttachment[]    findAll()
 * @method ShipmentAttachment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShipmentAttachmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShipmentAttachment::class);
    }

//    /**
//     * @return ShipmentAttachment[] Returns an array of ShipmentAttachment objects
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

//    public function findOneBySomeField($value): ?ShipmentAttachment
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
