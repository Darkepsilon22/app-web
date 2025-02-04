<?php

namespace App\Service;

use App\Entity\Users;
use App\Entity\Crypto;
use App\Entity\MouvementCrypto;
use App\Entity\MouvementSolde;
use App\Entity\CryptoUtilisateur;
use Doctrine\ORM\EntityManagerInterface;

class MouvementCryptoService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function acheterCrypto(Users $user, Crypto $crypto, float $quantite): array
    {
        $prixTotal = $crypto->getCurrentValeur() * $quantite;

        if ($user->getSolde() < $prixTotal) {
            return ['error' => 'Fonds insuffisants'];
        }

        $user->setSolde($user->getSolde() - $prixTotal);

        $mouvementCrypto = new MouvementCrypto();
        $mouvementCrypto->setEstAchat(true);
        $mouvementCrypto->setCrypto($crypto);
        $mouvementCrypto->setUser($user);

        $mouvementSolde = new MouvementSolde();
        $mouvementSolde->setSomme(-$prixTotal);
        $mouvementSolde->setDateMouvement(new \DateTimeImmutable());
        $mouvementSolde->setEstDepot(false);
        $mouvementSolde->setUser($user);

        $cryptoUtilisateur = $this->entityManager->getRepository(CryptoUtilisateur::class)
            ->findOneBy(['user' => $user, 'crypto' => $crypto]);

        if ($cryptoUtilisateur) {
            $cryptoUtilisateur->setQuantite($cryptoUtilisateur->getQuantite() + $quantite);
        } else {
            $cryptoUtilisateur = new CryptoUtilisateur();
            $cryptoUtilisateur->setUser($user);
            $cryptoUtilisateur->setCrypto($crypto);
            $cryptoUtilisateur->setQuantite($quantite);
            $this->entityManager->persist($cryptoUtilisateur);
        }

        $this->entityManager->persist($user);
        $this->entityManager->persist($mouvementCrypto);
        $this->entityManager->persist($mouvementSolde);
        
        $this->entityManager->flush();

        return ['success' => 'Achat effectué avec succès'];
    }
}
