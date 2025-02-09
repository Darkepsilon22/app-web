<?php

namespace App\Controller;

use App\Entity\CourCrypto;
use App\Entity\Crypto;
use App\Repository\CourCryptoRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CourCryptoController extends AbstractController {

    #[Route('/crypto/analyse', name: 'page_analyse_crypto'), ]
    public function getPage(): Response
    {
        return $this->render('cour_crypto/analyse.html.twig');
    }

    #[Route('/courcrypto/analyse', name: 'analyse_cour_crypto', methods: ['POST'])]
    public function getStatistiques(Request $request, CourCryptoRepository $courCryptoRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['typeAnalyse'], $data['crypto'], $data['dateMin'], $data['dateMax'])) {
            return $this->json(['error' => 'Données invalides'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $typeAnalyse = $data['typeAnalyse'];
            $cryptos = $data['crypto'];
            $dateMin = new DateTime($data['dateMin']);
            $dateMax = new DateTime($data['dateMax']);

            $resultats = $courCryptoRepository->getStatistiques($typeAnalyse, $cryptos, $dateMin, $dateMax);

            return $this->json(['resultats' => $resultats]);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Erreur lors de l\'analyse traitement : ', 'errormessage' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/crypto/graph/{id}', name: 'crypto_price_graph', methods: ['GET'])]
    public function getPriceUpdates(int $id, EntityManagerInterface $entityManager): Response
    {
        $crypto = $entityManager->getRepository(Crypto::class)->find($id);
    
        if (!$crypto) {
            return $this->render('graphe/graphe.html.twig', [
                'error' => 'Cryptomonnaie non trouvée', 
            ]);
        }
    
        $historiquePrix = $entityManager->getRepository(CourCrypto::class)
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
    
        return $this->json([
            'crypto' => $crypto,
            'historique_prix' => $prixHistorique, 
        ]);
    }
}
