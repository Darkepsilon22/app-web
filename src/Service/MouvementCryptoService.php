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

    // Méthode d'achat de crypto
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
        $mouvementCrypto->setDateMouvement(\DateTime::createFromImmutable(new \DateTimeImmutable()));
        $mouvementCrypto->setQuantite($quantite);  

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

        // Sauvegarder les changements
        $this->entityManager->flush();

        return ['success' => 'Achat effectué avec succès'];
    }

    // Nouvelle méthode pour la vente de crypto
    public function vendreCrypto(Users $user, Crypto $crypto, float $quantite): array
    {
        // Vérifier que l'utilisateur possède suffisamment de cryptomonnaies
        $cryptoUtilisateur = $this->entityManager->getRepository(CryptoUtilisateur::class)
            ->findOneBy(['user' => $user, 'crypto' => $crypto]);

        if (!$cryptoUtilisateur || $cryptoUtilisateur->getQuantite() < $quantite) {
            return ['error' => 'Quantité insuffisante de cryptomonnaies'];
        }

        // Calculer la valeur de la vente
        $prixTotal = $crypto->getCurrentValeur() * $quantite;

        // Mettre à jour le solde de l'utilisateur
        $user->setSolde($user->getSolde() + $prixTotal);

        // Enregistrer le mouvement crypto
        $mouvementCrypto = new MouvementCrypto();
        $mouvementCrypto->setEstAchat(false);  
        $mouvementCrypto->setCrypto($crypto);
        $mouvementCrypto->setUser($user);
        $mouvementCrypto->setDateMouvement(\DateTime::createFromImmutable(new \DateTimeImmutable()));
        $mouvementCrypto->setQuantite($quantite);  

        // Enregistrer le mouvement de solde
        $mouvementSolde = new MouvementSolde();
        $mouvementSolde->setSomme($prixTotal);
        $mouvementSolde->setDateMouvement(new \DateTimeImmutable());
        $mouvementSolde->setEstDepot(true);  
        $mouvementSolde->setUser($user);

        // Réduire la quantité de cryptomonnaie de l'utilisateur
        $cryptoUtilisateur->setQuantite($cryptoUtilisateur->getQuantite() - $quantite);

        // Si la quantité devient 0, on peut supprimer l'entité CryptoUtilisateur
        // if ($cryptoUtilisateur->getQuantite() <= 0) {
        //     $this->entityManager->remove($cryptoUtilisateur);
        // }

        // Persister les entités
        $this->entityManager->persist($user);
        $this->entityManager->persist($mouvementCrypto);
        $this->entityManager->persist($mouvementSolde);

        // Sauvegarder les changements
        $this->entityManager->flush();

        return ['success' => 'Vente effectuée avec succès'];
    }
}
