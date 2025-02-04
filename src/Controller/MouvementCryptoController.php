<?php

namespace App\Controller;

use App\Entity\Users;
use App\Entity\Crypto;
use App\Service\MouvementCryptoService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MouvementCryptoController extends AbstractController
{
    private MouvementCryptoService $mouvementCryptoService;
    private EntityManagerInterface $entityManager;

    public function __construct(MouvementCryptoService $mouvementCryptoService, EntityManagerInterface $entityManager)
    {
        $this->mouvementCryptoService = $mouvementCryptoService;
        $this->entityManager = $entityManager;
    }

    #[Route('/achat_crypto', name: 'achat_crypto', methods: ['POST'])]
    public function acheterCrypto(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $userId = $data['user_id'];
        $cryptoId = $data['crypto_id'];
        $quantite = $data['quantite'];

        $user = $this->entityManager->getRepository(Users::class)->find($userId);
        $crypto = $this->entityManager->getRepository(Crypto::class)->find($cryptoId);

        if (!$user || !$crypto) {
            return new JsonResponse(['error' => 'Utilisateur ou crypto non trouvé'], 400);
        }

        $result = $this->mouvementCryptoService->acheterCrypto($user, $crypto, $quantite);

        return new JsonResponse($result, isset($result['error']) ? 400 : 200);
    }

    #[Route('/vente_crypto', name: 'vente_crypto', methods: ['POST'])]
    public function vendreCrypto(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $userId = $data['user_id'];
        $cryptoId = $data['crypto_id'];
        $quantite = $data['quantite'];

        $user = $this->entityManager->getRepository(Users::class)->find($userId);
        $crypto = $this->entityManager->getRepository(Crypto::class)->find($cryptoId);

        if (!$user || !$crypto) {
            return new JsonResponse(['error' => 'Utilisateur ou crypto non trouvé'], 400);
        }

        // Appeler le service de vente
        $result = $this->mouvementCryptoService->vendreCrypto($user, $crypto, $quantite);

        return new JsonResponse($result, isset($result['error']) ? 400 : 200);
    }
}
