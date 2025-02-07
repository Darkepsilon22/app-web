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

    public function acheterCrypto(Users $user, Crypto $crypto, float $quantite, string $dateMouvement): array
{
    $dernierHistoriqueCommission = $this->entityManager->getRepository(HistoriqueCommission::class)
        ->findOneBy([], ['dateHistoriquePourcentage' => 'DESC']);

    if (!$dernierHistoriqueCommission) {
        return ['error' => 'Aucune commission définie'];
    }

    $commissionPourcentage = $dernierHistoriqueCommission->getValeurHistoriquePourcentage();

    // Calcul du prix total du crypto sans la commission
    $prixCrypto = $crypto->getCurrentValeur() * $quantite;

    // Calcul de la commission basée sur la valeur du crypto (pas sur le prix total)
    $commissionValeur = ($crypto->getCurrentValeur() * $commissionPourcentage / 100) * $quantite;

    // Calcul du prix total que l'utilisateur devra payer (prix crypto + commission)
    $prixTotal = $prixCrypto + $commissionValeur;

    if ($user->getSolde() < $prixTotal) {
        return ['error' => 'Fonds insuffisants'];
    }

    // Mise à jour du solde de l'utilisateur
    $user->setSolde($user->getSolde() - $prixTotal);

    // Enregistrement du mouvement de solde (retrait)
    $mouvementSolde = new MouvementSolde();
    $mouvementSolde->setSomme($prixTotal);
    $mouvementSolde->setDateMouvement(new \DateTimeImmutable($dateMouvement));
    $mouvementSolde->setEstDepot(false); // Retrait
    $mouvementSolde->setUser($user);

    // Mise à jour de la quantité de crypto de l'utilisateur
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

    // Enregistrement du mouvement d'achat
    $mouvementCrypto = new MouvementCrypto();
    $mouvementCrypto->setEstAchat(true);
    $mouvementCrypto->setCrypto($crypto);
    $mouvementCrypto->setUser($user);
    $mouvementCrypto->setDateMouvement(new \DateTime($dateMouvement));
    $mouvementCrypto->setQuantite($quantite);

    // Enregistrement de la commission
    $commission = new Commission();
    $commission->setPourcentage($commissionPourcentage);
    $commission->setValeur($commissionValeur); // Commission calculée sur la valeur brute
    $commission->setMouvementCrypto($mouvementCrypto);

    // Persistance des données
    $this->entityManager->persist($user);
    $this->entityManager->persist($mouvementSolde);
    $this->entityManager->persist($mouvementCrypto);
    $this->entityManager->persist($commission);
    $this->entityManager->persist($cryptoUtilisateur);

    $this->entityManager->flush();

    return ['success' => 'Achat effectué avec succès'];
}

    

public function vendreCrypto(Users $user, Crypto $crypto, float $quantite, string $dateMouvement): array
{
    // Récupérer la commission la plus récente
    $dernierHistoriqueCommission = $this->entityManager->getRepository(HistoriqueCommission::class)
        ->findOneBy([], ['dateHistoriquePourcentage' => 'DESC']);

    if (!$dernierHistoriqueCommission) {
        return ['error' => 'Aucune commission définie'];
    }

    $commissionPourcentage = $dernierHistoriqueCommission->getValeurHistoriquePourcentage();

    // Calcul du prix total sans commission (la commission ne s’applique qu’après la vente)
    $prixTotal = $crypto->getCurrentValeur() * $quantite;

    // Récupérer la quantité de crypto détenue par l'utilisateur
    $cryptoUtilisateur = $this->entityManager->getRepository(CryptoUtilisateur::class)
        ->findOneBy(['user' => $user, 'crypto' => $crypto]);

    if (!$cryptoUtilisateur || $cryptoUtilisateur->getQuantite() < $quantite) {
        return ['error' => 'Quantité de crypto insuffisante'];
    }

    // Mise à jour du solde utilisateur après vente
    $user->setSolde($user->getSolde() + $prixTotal);

    // Enregistrement du mouvement de solde (dépôt)
    $mouvementSolde = new MouvementSolde();
    $mouvementSolde->setSomme($prixTotal);
    $mouvementSolde->setDateMouvement(new \DateTimeImmutable($dateMouvement));
    $mouvementSolde->setEstDepot(true); // Dépôt
    $mouvementSolde->setUser($user);

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

    // Persistance des données
    $this->entityManager->persist($user);
    $this->entityManager->persist($cryptoUtilisateur);
    $this->entityManager->persist($mouvementSolde);
    $this->entityManager->persist($mouvementCrypto);
    $this->entityManager->persist($commission);

    $this->entityManager->flush();

    return ['success' => 'Vente effectuée avec succès'];
}




}
