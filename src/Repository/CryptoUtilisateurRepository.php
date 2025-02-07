<?php

namespace App\Repository;

use App\Entity\CryptoUtilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\NonUniqueResultException;

/**
 * @extends ServiceEntityRepository<CryptoUtilisateur>
 */
class CryptoUtilisateurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CryptoUtilisateur::class);
    }

    public function findByUserAndCrypto(?int $id_users, ?int $id_crypto): ?float
    {
        try {
            return $this->createQueryBuilder('c')
                ->select('c.quantite')
                ->where('c.user = :id_users')
                ->andWhere('c.crypto = :id_crypto')
                ->setParameter('id_users', $id_users)
                ->setParameter('id_crypto', $id_crypto)
                ->getQuery()
                ->getSingleScalarResult() ?? 0;
        } catch (NoResultException | NonUniqueResultException $e) {
            return 0;
        }
    }
    
    

    //    /**
    //     * @return CryptoUtilisateur[] Returns an array of CryptoUtilisateur objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?CryptoUtilisateur
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
