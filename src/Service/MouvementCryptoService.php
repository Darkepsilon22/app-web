<?php

namespace App\Service;

use App\Entity\Users;
use App\Entity\Crypto;
use App\Entity\MouvementCrypto;
use App\Entity\MouvementSolde;
use App\Entity\CryptoUtilisateur;
use App\Entity\Commission;
use App\Entity\HistoriqueCommission;
use Doctrine\ORM\EntityManagerInterface;

class MouvementCryptoService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function acheterCrypto(Users $user, Crypto $crypto, float $quantite, string $dateMouvement, float $commissionPourcentage): array
{
    $prixTotal = $crypto->getCurrentValeur() * (1 + ($commissionPourcentage / 100)) * $quantite;

    if ($user->getSolde() < $prixTotal) {
        return ['error' => 'Fonds insuffisants'];
    }

    // Déduire le prix total du solde de l'utilisateur
    $user->setSolde($user->getSolde() - $prixTotal);

    // Vérifier si l'utilisateur possède déjà cette crypto
    $cryptoUtilisateur = $this->entityManager->getRepository(CryptoUtilisateur::class)
        ->findOneBy(['user' => $user, 'crypto' => $crypto]);

    if ($cryptoUtilisateur) {
        // L'utilisateur possède déjà cette crypto, mise à jour de la quantité
        $cryptoUtilisateur->setQuantite($cryptoUtilisateur->getQuantite() + $quantite);
    } else {
        // L'utilisateur n'a pas encore cette crypto, créer une nouvelle entrée
        $cryptoUtilisateur = new CryptoUtilisateur();
        $cryptoUtilisateur->setUser($user);
        $cryptoUtilisateur->setCrypto($crypto);
        $cryptoUtilisateur->setQuantite($quantite);

        $this->entityManager->persist($cryptoUtilisateur);
    }

    // Enregistrer le mouvement d'achat
    $mouvementCrypto = new MouvementCrypto();
    $mouvementCrypto->setEstAchat(true);
    $mouvementCrypto->setCrypto($crypto);
    $mouvementCrypto->setUser($user);
    $mouvementCrypto->setDateMouvement(new \DateTime($dateMouvement));
    $mouvementCrypto->setQuantite($quantite);

    // Calculer et enregistrer la commission
    $commissionValeur = ($prixTotal * $commissionPourcentage) / 100;
    $commission = new Commission();
    $commission->setPourcentage($commissionPourcentage);
    $commission->setValeur($commissionValeur);
    $commission->setMouvementCrypto($mouvementCrypto);

    // Historique de la commission
    $historiqueCommission = new HistoriqueCommission();
    $historiqueCommission->setDateHistorique(new \DateTimeImmutable());
    $historiqueCommission->setValeurHistorique($commissionValeur);

    // Persistance des données
    $this->entityManager->persist($user);
    $this->entityManager->persist($mouvementCrypto);
    $this->entityManager->persist($commission);
    $this->entityManager->persist($historiqueCommission);
    $this->entityManager->persist($cryptoUtilisateur);

    $this->entityManager->flush();

    return ['success' => 'Achat effectué avec succès'];
}

public function vendreCrypto(Users $user, Crypto $crypto, float $quantite, string $dateMouvement, float $commissionPourcentage): array
{
    $prixTotal = $crypto->getCurrentValeur() * $quantite;

    // Récupérer la quantité de crypto détenue par l'utilisateur
    $cryptoUtilisateur = $this->entityManager->getRepository(CryptoUtilisateur::class)
        ->findOneBy(['user' => $user, 'crypto' => $crypto]);

    if (!$cryptoUtilisateur || $cryptoUtilisateur->getQuantite() < $quantite) {
        return ['error' => 'Quantité de crypto insuffisante'];
    }

    // Mise à jour du solde utilisateur
    $user->setSolde($user->getSolde() + $prixTotal);

    // Mise à jour de la quantité de crypto détenue
    $nouvelleQuantite = $cryptoUtilisateur->getQuantite() - $quantite;
    $cryptoUtilisateur->setQuantite(max($nouvelleQuantite, 0)); // Assurer que la quantité reste >= 0

    // Enregistrement du mouvement de vente
    $mouvementCrypto = new MouvementCrypto();
    $mouvementCrypto->setEstAchat(false);
    $mouvementCrypto->setCrypto($crypto);
    $mouvementCrypto->setUser($user);
    $mouvementCrypto->setDateMouvement(new \DateTime($dateMouvement));
    $mouvementCrypto->setQuantite($quantite);

    // Calcul et enregistrement de la commission
    $commissionValeur = ($prixTotal * $commissionPourcentage) / 100;
    $commission = new Commission();
    $commission->setPourcentage($commissionPourcentage);
    $commission->setValeur($commissionValeur);
    $commission->setMouvementCrypto($mouvementCrypto);

    // Historique de la commission
    $historiqueCommission = new HistoriqueCommission();
    $historiqueCommission->setDateHistorique(new \DateTimeImmutable());
    $historiqueCommission->setValeurHistorique($commissionValeur);

    // Persistance des données
    $this->entityManager->persist($user);
    $this->entityManager->persist($cryptoUtilisateur);
    $this->entityManager->persist($mouvementCrypto);
    $this->entityManager->persist($commission);
    $this->entityManager->persist($historiqueCommission);

    $this->entityManager->flush();

    return ['success' => 'Vente effectuée avec succès'];
}


}
