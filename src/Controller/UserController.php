<?php
// UserController.php

namespace App\Controller;

use App\Entity\Users;
use App\Entity\MouvementSolde;
use App\Repository\MouvementCryptoRepository;
use App\Repository\TokenConnexionRepository;
use App\Service\TokenConnexionService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class UserController extends AbstractController
{
    private $tokenConnexionService;

    public function __construct(TokenConnexionService $tokenConnexionService)
    {
        $this->tokenConnexionService = $tokenConnexionService;
    }

    #[Route('/user/transactions/', name: 'transactions_user', methods: ['GET'])]
    public function index(Request $request,MouvementCryptoRepository $mouvementCryptoRepository, TokenConnexionRepository $tokenRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->tokenConnexionService->getUserFromToken($request, $tokenRepository, $entityManager);
        if (!$user) {
            return $this->render('accueil.html.twig');
        }

        $historique = $mouvementCryptoRepository->getHistoriqueByUser($user->getIdUsers());

        return $this->render('historique/transactions.html.twig', [
            'historique' => $historique,
            'user' => $user,
        ]);
    }
    
    #[Route('/user/depot_retrait/', name: 'user_depot_retrait', methods: ['GET', 'POST'])]
    public function depotRetrait(Request $request, TokenConnexionRepository $tokenRepository, EntityManagerInterface $entityManager): Response
    {

        $user = $this->tokenConnexionService->getUserFromToken($request, $tokenRepository, $entityManager);
        if (!$user) {
            return $this->render('accueil.html.twig');
        }

        $mouvementsSolde = $entityManager->getRepository(MouvementSolde::class)
            ->findBy(['user' => $user], ['dateMouvement' => 'DESC']); 

        if ($request->isMethod('POST')) {
            $somme = $request->request->get('somme');
            $operation = $request->request->get('operation');

            if (empty($somme) || !is_numeric($somme)) {
                return $this->render('user/depot_retrait.html.twig', [
                    'user' => $user,
                    'error' => 'Le montant doit être un nombre valide.',
                    'mouvementsSolde' => $mouvementsSolde
                ]);
            }

            if ($user->getSolde() < $somme &&  $operation != 'depot') {
                return $this->render('user/depot_retrait.html.twig', [
                    'user' => $user,
                    'error' => 'Solde insuffiante.',
                    'mouvementsSolde' => $mouvementsSolde
                ]);
            }

            $mouvementSolde = new MouvementSolde();
            $mouvementSolde->setSomme((string)$somme);
            $mouvementSolde->setEstDepot($operation == 'depot');
            $mouvementSolde->setUser($user);
            $mouvementSolde->setDateMouvement(new \DateTimeImmutable());
            $mouvementSolde->setStatut('en_attente');

            $entityManager->persist($mouvementSolde);
            $entityManager->flush();

            return $this->render('user/depot_retrait.html.twig', [
                'user' => $user,
                'success' => 'Votre demande de '.$operation.' a été enregistrée et est en attente de validation.',
                'mouvementsSolde' => $mouvementsSolde
                
            ]);
        }

        return $this->render('user/depot_retrait.html.twig', [
            'user' => $user,
            'mouvementsSolde' => $mouvementsSolde
        ]);
    }
}
