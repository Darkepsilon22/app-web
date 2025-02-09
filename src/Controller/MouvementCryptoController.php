<?php

namespace App\Controller;

use App\Entity\Users;
use App\Entity\Crypto;
use App\Entity\CourCrypto;
use App\Entity\HistoriqueCommission;

use App\Service\MouvementCryptoService;
use App\Repository\MouvementCryptoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MouvementCryptoController extends AbstractController
{
    private MouvementCryptoService $mouvementCryptoService;
    private EntityManagerInterface $entityManager;
    private MouvementCryptoRepository $mouvementCryptoRepository;

    public function __construct(
        MouvementCryptoService $mouvementCryptoService, 
        EntityManagerInterface $entityManager, 
        MouvementCryptoRepository $mouvementCryptoRepository
    ) {
        $this->mouvementCryptoService = $mouvementCryptoService;
        $this->entityManager = $entityManager;
        $this->mouvementCryptoRepository = $mouvementCryptoRepository;
    }

    #[Route('/achat_crypto', name: 'achat_crypto', methods: ['POST'])]
public function acheterCrypto(Request $request): JsonResponse
{
    $data = json_decode($request->getContent(), true);
    
    if (!isset($data['user_id'], $data['crypto_id'], $data['quantite'], $data['date_mouvement'])) {
        return new JsonResponse(['error' => 'Paramètres manquants'], 400);
    }

    $user = $this->entityManager->getRepository(Users::class)->find($data['user_id']);
    $crypto = $this->entityManager->getRepository(Crypto::class)->find($data['crypto_id']);

    if (!$user || !$crypto) {
        return new JsonResponse(['error' => 'Utilisateur ou crypto non trouvé'], 400);
    }

    // Récupération de la commission la plus récente
    $commission = $this->entityManager->getRepository(HistoriqueCommission::class)
    ->findOneBy([], ['dateHistoriquePourcentage' => 'DESC']);


    if (!$commission) {
        return new JsonResponse(['error' => 'Aucune commission définie'], 400);
    }

    $result = $this->mouvementCryptoService->acheterCrypto(
        $user,
        $crypto,
        $data['quantite'],
        $data['date_mouvement']
    );

    return new JsonResponse($result, isset($result['error']) ? 400 : 200);
}

#[Route('/vente_crypto', name: 'vente_crypto', methods: ['POST'])]
public function vendreCrypto(Request $request): JsonResponse
{
    $data = json_decode($request->getContent(), true);
    
    if (!isset($data['user_id'], $data['crypto_id'], $data['quantite'], $data['date_mouvement'])) {
        return new JsonResponse(['error' => 'Paramètres manquants'], 400);
    }

    $user = $this->entityManager->getRepository(Users::class)->find($data['user_id']);
    $crypto = $this->entityManager->getRepository(Crypto::class)->find($data['crypto_id']);

    if (!$user || !$crypto) {
        return new JsonResponse(['error' => 'Utilisateur ou crypto non trouvé'], 400);
    }

    // Récupération de la commission la plus récente
    $commission = $this->entityManager->getRepository(HistoriqueCommission::class)
    ->findOneBy([], ['dateHistoriquePourcentage' => 'DESC']);


    if (!$commission) {
        return new JsonResponse(['error' => 'Aucune commission définie'], 400);
    }

    $result = $this->mouvementCryptoService->vendreCrypto(
        $user,
        $crypto,
        $data['quantite'],
        $data['date_mouvement'],
        $commission->getValeurHistoriquePourcentage()
    );

    return new JsonResponse($result, isset($result['error']) ? 400 : 200);
}

    #[Route('/transactions', name: 'transactions_users', methods: ['GET'])]
    public function historiques(): Response
    {
        $historique = $this->mouvementCryptoRepository->getHistorique();

        
        return $this->render('historique/transactions.html.twig', [
            'historique' => $historique,
        ]);
    }
}
