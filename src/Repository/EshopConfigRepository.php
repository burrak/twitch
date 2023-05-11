<?php

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\EshopConfig;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EshopConfig>
 *
 * @method EshopConfig|null find($id, $lockMode = null, $lockVersion = null)
 * @method EshopConfig|null findOneBy(array $criteria, array $orderBy = null)
 * @method EshopConfig[]    findAll()
 * @method EshopConfig[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EshopConfigRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EshopConfig::class);
    }

    public function save(EshopConfig $entity, bool $flush = false): void
    {
        /** @phpstan-ignore-next-line */
        $this->getEntityManager()->merge($entity);

        if ($flush) {
            return;
        }

        $this->getEntityManager()->flush();
    }

    public function remove(EshopConfig $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if (!$flush) {
            return;
        }

        $this->getEntityManager()->flush();
    }

//    /**
//     * @return EshopConfig[] Returns an array of EshopConfig objects
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

//    public function findOneBySomeField($value): ?EshopConfig
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
