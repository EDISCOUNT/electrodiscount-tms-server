<?php

namespace App\Repository\Mailing;

use App\Entity\Mailing\EmailAttachment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EmailAttachment>
 *
 * @method EmailAttachment|null find($id, $lockMode = null, $lockVersion = null)
 * @method EmailAttachment|null findOneBy(array $criteria, array $orderBy = null)
 * @method EmailAttachment[]    findAll()
 * @method EmailAttachment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmailAttachmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EmailAttachment::class);
    }

//    /**
//     * @return EmailAttachment[] Returns an array of EmailAttachment objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?EmailAttachment
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
