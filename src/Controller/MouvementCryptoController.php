<?php

namespace App\Controller;

use App\Entity\Users;
use App\Entity\Crypto;
use App\Entity\CourCrypto;
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
        $userId = $data['user_id'];
        $cryptoId = $data['crypto_id'];
        $quantite = $data['quantite'];
        $dateMouvement = $data['date_mouvement'];

        $user = $this->entityManager->getRepository(Users::class)->find($userId);
        $crypto = $this->entityManager->getRepository(Crypto::class)->find($cryptoId);

        if (!$user || !$crypto) {
            return new JsonResponse(['error' => 'Utilisateur ou crypto non trouvé'], 400);
        }

        $result = $this->mouvementCryptoService->acheterCrypto($user, $crypto, $quantite, $dateMouvement);

        return new JsonResponse($result, isset($result['error']) ? 400 : 200);
    }

    #[Route('/vente_crypto', name: 'vente_crypto', methods: ['POST'])]
    public function vendreCrypto(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $userId = $data['user_id'];
        $cryptoId = $data['crypto_id'];
        $quantite = $data['quantite'];
        $dateMouvement = $data['date_mouvement'];


        $user = $this->entityManager->getRepository(Users::class)->find($userId);
        $crypto = $this->entityManager->getRepository(Crypto::class)->find($cryptoId);

        if (!$user || !$crypto) {
            return new JsonResponse(['error' => 'Utilisateur ou crypto non trouvé'], 400);
        }

        $result = $this->mouvementCryptoService->vendreCrypto($user, $crypto, $quantite, $dateMouvement);

        return new JsonResponse($result, isset($result['error']) ? 400 : 200);
    }

    #[Route('/cryptocurrency/{id}', name: 'crypto_price_updates', methods: ['GET'])]
    public function getPriceUpdates(int $id): Response
    {
        $crypto = $this->entityManager->getRepository(Crypto::class)->find($id);
    
        if (!$crypto) {
            throw $this->createNotFoundException('Cryptomonnaie non trouvée');
        }
    
        $historiquePrix = $this->entityManager->getRepository(CourCrypto::class)
            ->findBy(['crypto' => $crypto], ['instant' => 'ASC']);
    
        $prixHistorique = [];
        $previousTime = null;
    
        foreach ($historiquePrix as $prix) {
            $currentTime = $prix->getInstant();
            
            if ($previousTime === null || $currentTime->getTimestamp() - $previousTime->getTimestamp() >= 10) {
                $prixHistorique[] = [
                    'date' => $currentTime->format('Y-m-d H:i:s'),
                    'valeur_dollar' => $prix->getValeurDollar()
                ];
                $previousTime = $currentTime;
            }
        }
    
        return $this->render('graphe/graphe.html.twig', [
            'crypto' => $crypto,
            'historique_prix' => $prixHistorique, 
        ]);
    }

    #[Route('/historique/{userId}', name: 'crypto_price_updates', methods: ['GET'])]
    public function index(int $userId): Response
    {
        $historique = $this->mouvementCryptoRepository->getHistoriqueByUser($userId);

        return $this->render('historique/historique.html.twig', [
            'historique' => $historique,
        ]);
    }
}
