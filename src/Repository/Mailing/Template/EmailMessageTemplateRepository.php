<?php

namespace App\Repository\Mailing\Template;

use App\Entity\Mailing\Template\EmailMessageTemplate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EmailMessageTemplate>
 *
 * @method EmailMessageTemplate|null find($id, $lockMode = null, $lockVersion = null)
 * @method EmailMessageTemplate|null findOneBy(array $criteria, array $orderBy = null)
 * @method EmailMessageTemplate[]    findAll()
 * @method EmailMessageTemplate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmailMessageTemplateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EmailMessageTemplate::class);
    }

//    /**
//     * @return EmailMessageTemplate[] Returns an array of EmailMessageTemplate objects
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

//    public function findOneBySomeField($value): ?EmailMessageTemplate
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
