<?php

namespace App\Controller;

use App\Entity\Crypto;
use App\Repository\CourCryptoRepository;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CourCryptoController extends AbstractController{
    #[Route('/crypto/analyse', name: 'analyse_crypto')]
    public function getStatistiques(Request $request, ManagerRegistry $doctrine, CourCryptoRepository $courCryptoRepository): Response
    {
        $resultats = null;

        if ($request->isMethod('POST')) {
            $typeAnalyse = $request->request->get('typeAnalyse');
            $cryptos = $request->request->all('crypto');
            $dateMin = new DateTime($request->request->get('dateMin'));
            $dateMax = new DateTime($request->request->get('dateMax'));

            $resultats = $courCryptoRepository->getStatistiques($typeAnalyse, $cryptos, $dateMin, $dateMax);
        }
        $repositorycrypto = $doctrine->getRepository(Crypto::class);
        $cryptos = $repositorycrypto->findAll();
        
        return $this->render('cour_crypto/analyse.html.twig', [
            'resultats' => $resultats,
            'cryptos' => $cryptos
        ]);
    }
}
