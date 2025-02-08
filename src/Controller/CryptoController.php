<?php

namespace App\Controller;

use App\Entity\Crypto;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

final class CryptoController extends AbstractController{
    #[Route('/cryptos', name: 'liste_crypto', methods: ['GET'])]
    public function getListeCryptos(ManagerRegistry $doctrine, SerializerInterface $serializer): JsonResponse
    {
        $repositorycrypto = $doctrine->getRepository(Crypto::class);
        $cryptos = $repositorycrypto->findAll();
    
        return $this->json(['cryptos' => $cryptos]);
    }
    
}
