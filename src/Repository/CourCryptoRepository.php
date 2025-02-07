<?php

namespace App\Repository;

use App\Entity\CourCrypto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CourCrypto>
 */
class CourCryptoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CourCrypto::class);
    }

//    /**
//     * @return CourCrypto[] Returns an array of CourCrypto objects
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

//    public function findOneBySomeField($value): ?CourCrypto
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    // public function getStatistiques(string $typeAnalyse, array $cryptos, \DateTime $dateMin, \DateTime $dateMax): array
    // {
    //     $qb = $this->createQueryBuilder('c')
    //         ->join('c.crypto', 'cr') 
    //         ->where('c.instant BETWEEN :dateMin AND :dateMax')
    //         ->setParameter('dateMin', $dateMin)
    //         ->setParameter('dateMax', $dateMax);

    //     if (!in_array('Tous', $cryptos)) {
    //         $qb->andWhere('c.crypto IN (:cryptos)')
    //         ->setParameter('cryptos', $cryptos);
    //     }

    //     switch ($typeAnalyse) {
    //         case 'min':
    //             $qb->select('cr.intitule AS crypto_nom', 'IDENTITY(c.crypto) AS crypto_id', 'MIN(c.valeur_dollar) AS valeur');
    //             break;
    //         case 'max':
    //             $qb->select('cr.intitule AS crypto_nom', 'IDENTITY(c.crypto) AS crypto_id', 'MAX(c.valeur_dollar) AS valeur');
    //             break;
    //         case 'moyenne':
    //             $qb->select('cr.intitule AS crypto_nom', 'IDENTITY(c.crypto) AS crypto_id', 'AVG(c.valeur_dollar) AS valeur');
    //             break;
    //         case 'ecart-type':
    //             $qb->select('cr.intitule AS crypto_nom', 'IDENTITY(c.crypto) AS crypto_id', 'STDDEV(c.valeur_dollar) AS valeur');
    //             break;
    //         case '1-quartile':
    //             $qb->select('cr.intitule AS crypto_nom', 'IDENTITY(c.crypto) AS crypto_id', 'PERCENTILE_CONT(0.25) WITHIN GROUP (ORDER BY c.valeur_dollar) AS valeur');
    //             break;
    //         default:
    //             throw new \InvalidArgumentException("Type d'analyse non valide");
    //     }

    //     $qb->groupBy('c.crypto', 'cr.intitule');
    //     $qb->orderBy('c.crypto');

    //     $result = $qb->getQuery()->getResult();

    //     $statistiques = [];
    //     foreach ($result as $row) {
    //         $statistiques[$row['crypto_id']] = [
    //             'crypto_id' => $row['crypto_id'],
    //             'crypto_nom' => $row['crypto_nom'],
    //             'valeur' => $row['valeur'],
    //         ];
    //     }

    //     return $statistiques;
    // }

    public function getStatistiques(string $typeAnalyse, array $cryptos, \DateTime $dateMin, \DateTime $dateMax): array
    {
        $conn = $this->getEntityManager()->getConnection();
    
        $sql = "
            SELECT cr.intitule AS crypto_nom, c.id_crypto AS crypto_id, ";
    
        switch ($typeAnalyse) {
            case 'min':
                $sql .= "MIN(c.valeur_dollar) AS valeur ";
                break;
            case 'max':
                $sql .= "MAX(c.valeur_dollar) AS valeur ";
                break;
            case 'moyenne':
                $sql .= "AVG(c.valeur_dollar) AS valeur ";
                break;
            case 'ecart-type':
                $sql .= "STDDEV_POP(c.valeur_dollar) AS valeur ";
                break;
            case '1-quartile':
                $sql .= "PERCENTILE_CONT(0.25) WITHIN GROUP (ORDER BY c.valeur_dollar) AS valeur ";
                break;
            default:
                throw new \InvalidArgumentException("Type d'analyse non valide");
        }
    
        $sql .= "
            FROM cour_crypto c
            JOIN crypto cr ON c.id_crypto = cr.id_crypto
            WHERE c.instant BETWEEN :dateMin AND :dateMax
        ";
    
        $cryptosList = (!in_array('Tous', $cryptos) && !empty($cryptos))
            ? implode(',', array_map(fn($crypto) => (int) $crypto, $cryptos))
            : null;
    
        if ($cryptosList !== null) {
            $sql .= "AND c.id_crypto IN ($cryptosList) ";
        }
    
        $sql .= "GROUP BY c.id_crypto, cr.intitule ORDER BY c.id_crypto";
    
        // Exécuter la requête
        $stmt = $conn->executeQuery($sql, [
            'dateMin' => $dateMin->format('Y-m-d H:i:s'),
            'dateMax' => $dateMax->format('Y-m-d H:i:s'),
        ]);
    
        $result = $stmt->fetchAllAssociative();
    
        // Construire le tableau de statistiques
        $statistiques = [];
        foreach ($result as $row) {
            $statistiques[$row['crypto_id']] = [
                'crypto_id' => $row['crypto_id'],
                'crypto_nom' => $row['crypto_nom'],
                'valeur' => $row['valeur'],
            ];
        }
    
        return $statistiques;
    }
     
    
}
