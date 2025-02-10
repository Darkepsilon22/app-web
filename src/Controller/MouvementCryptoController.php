<?php

namespace App\Controller;

use App\Entity\Users;
use App\Entity\Crypto;
use App\Entity\CourCrypto;
use App\Entity\HistoriqueCommission;

use App\Service\MouvementCryptoService;
use App\Repository\MouvementCryptoRepository;
use App\Repository\TokenConnexionRepository;
use App\Service\TokenConnexionService;
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

    private TokenConnexionService $tokenConnexionService;

    public function __construct(
        MouvementCryptoService $mouvementCryptoService, 
        EntityManagerInterface $entityManager, 
        MouvementCryptoRepository $mouvementCryptoRepository,
        TokenConnexionService $tokenConnexionService
    ) {
        $this->mouvementCryptoService = $mouvementCryptoService;
        $this->entityManager = $entityManager;
        $this->mouvementCryptoRepository = $mouvementCryptoRepository;
        $this->tokenConnexionService = $tokenConnexionService;
    }


    #[Route('/crypto/achat_vente', name: 'achat_vente_crypto', methods: ['POST'])]
    public function achat_vente(Request $request, TokenConnexionRepository $tokenRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->tokenConnexionService->getUserFromToken($request, $tokenRepository, $entityManager);
        if (!$user) {
            return $this->render('accueil.html.twig');
        }
    
        $operation = $request->request->get('operation');
        $idCrypto = $request->request->get('idcrypto');
        $valueCrypto = $request->request->get('valueCrypto');
        $dateMouvement = (new \DateTime())->format('Y-m-d H:i:s');

    
        if (empty($user) || empty($idCrypto) || empty($valueCrypto)) {
            return $this->render('user/achat_vente_crypto.html.twig', [
                'user' => $user,
                'error' => 'Paramètres manquants'
            ]);
        }
    
        $crypto = $entityManager->getRepository(Crypto::class)->find($idCrypto);
        if (!$crypto) {
            return $this->render('user/achat_vente_crypto.html.twig', [
                'user' => $user,
                'error' => 'Crypto non trouvé'
            ]);
        }
    
        // Récupération de la commission la plus récente
        $commission = $entityManager->getRepository(HistoriqueCommission::class)
            ->findOneBy([], ['dateHistoriquePourcentage' => 'DESC']);
    
        if (!$commission) {
            return $this->render('user/achat_vente_crypto.html.twig', [
                'user' => $user,
                'error' => 'Aucune commission définie'
            ]);
        }
    
        // Traitement de l'opération achat ou vente
        $result = match ($operation) {
            'achat' => $this->mouvementCryptoService->acheterCrypto($user, $crypto, $valueCrypto, $dateMouvement),
            'vente' => $this->mouvementCryptoService->vendreCrypto($user, $crypto, $valueCrypto, $dateMouvement),
            default => ['error' => 'Opération invalide'],
        };
    
        return $this->render('user/achat_vente_crypto.html.twig', array_merge([
            'user' => $user,
        ], isset($result['error']) 
            ? ['error' => $result['error']] 
            : ['success' => $result['success']]));
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
}
