<?php

declare(strict_types = 1);

namespace App\Repository;

use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function save(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if (!$flush) {
            return;
        }
        $this->getEntityManager()->flush();
    }

    public function remove(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if (!$flush) {
            return;
        }
        $this->getEntityManager()->flush();
    }

    public function getActiveProductsByStreamer(User $streamer): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.dateFrom <= :currentDate OR p.dateFrom IS NULL')
            ->andWhere('p.dateTo >= :currentDate OR p.dateTo IS NULL')
            ->andWhere('p.active = 1')
            ->andWhere('p.streamer = :streamer')
            ->andWhere('p.totalLimit > (SELECT SUM(oi.quantity) FROM App\Entity\OrderItem oi WHERE oi.product = p.id) OR p.totalLimit IS NULL')
            ->setParameter('streamer', $streamer->getId()->toBinary())
            /*->setParameters(new ArrayCollection([
                new Parameter('streamer', $streamer->getId()->, 'uuid_binary')
            ]))*/
            ->setParameter('currentDate', new \DateTime())
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return Product[] Returns an array of Product objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Product
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
