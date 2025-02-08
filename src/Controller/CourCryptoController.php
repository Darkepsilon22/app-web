<?php

namespace App\Controller;

use App\Repository\CourCryptoRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CourCryptoController extends AbstractController{
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
}
