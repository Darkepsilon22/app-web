<?php

namespace App\Repository;

use App\Entity\MouvementCrypto;
use App\Entity\Users;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MouvementCryptoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MouvementCrypto::class);
    }

    public function getHistoriqueByUser(int $userId)
{
    return $this->createQueryBuilder('m')
        ->select(
            'm.id_mouvement_crypto AS idMouvementCrypto', 
            'CASE WHEN m.estAchat = true THEN :achat ELSE :vente END AS typeMouvement',
            'c.id_crypto AS idCrypto', 
            'c.intitule', 
            'u.prenom', 
            'u.nom',    
            'm.dateMouvement' ,
            'm.quantite',
            'm.valeurCrypto'
        )
        ->join('m.crypto', 'c') 
        ->join('m.user', 'u')   
        ->where('m.user = :userId') 
        ->setParameter('userId', $userId)
        ->setParameter('achat', 'Achat')
        ->setParameter('vente', 'Vente')
        ->orderBy('m.dateMouvement', 'DESC')
        ->getQuery()
        ->getResult(); 
}

public function getHistorique()
{
    return $this->createQueryBuilder('m')
        ->select(
            'm.id_mouvement_crypto AS idMouvementCrypto', 
            'CASE WHEN m.estAchat = true THEN :achat ELSE :vente END AS typeMouvement',
            'c.id_crypto AS idCrypto', 
            'c.intitule', 
            'u.prenom', 
            'u.nom',    
            'm.dateMouvement' ,
            'm.quantite',
            'm.valeurCrypto'
        )
        ->join('m.crypto', 'c') 
        ->join('m.user', 'u')   
        ->setParameter('achat', 'Achat')
        ->setParameter('vente', 'Vente')
        ->orderBy('m.dateMouvement', 'DESC')
        ->getQuery()
        ->getResult(); 
}

public function getTotalAchatByUser(int $userId): float
{
    return (float) $this->createQueryBuilder('m')
        ->select('SUM(m.quantite * m.valeurCrypto) AS totalAchat')
        ->where('m.user = :userId')
        ->andWhere('m.estAchat = true')
        ->setParameter('userId', $userId)
        ->getQuery()
        ->getSingleScalarResult();
}

public function getTotalVenteByUser(int $userId): float
{
    return (float) $this->createQueryBuilder('m')
        ->select('SUM(m.quantite * m.valeurCrypto) AS totalVente')
        ->where('m.user = :userId')
        ->andWhere('m.estAchat = false')
        ->setParameter('userId', $userId)
        ->getQuery()
        ->getSingleScalarResult();
}

}
