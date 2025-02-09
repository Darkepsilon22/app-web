<?php

namespace App\Controller;

use App\Entity\Users;
use App\Repository\TokenConnexionRepository;
use App\Service\TokenConnexionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AppController extends AbstractController {

    private $tokenConnexionService;

    public function __construct(TokenConnexionService $tokenConnexionService)
    {
        $this->tokenConnexionService = $tokenConnexionService;
    }

    #[Route('/', name: 'page_acceuil')]
    public function index(Request $request, TokenConnexionRepository $tokenRepository, EntityManagerInterface $entityManager): Response
    {


        $repository = $entityManager->getRepository(Users::class);
        $user = $repository->find(1);
        // $user = $this->tokenConnexionService->getUserFromToken($request, $tokenRepository, $entityManager);
        if ($user) {
            return $this->render('accueil.html.twig', [
                'user' => $user,
                'controller_name' => 'AppController',
            ]);
        }
    
        return $this->render('accueil.html.twig', [
            'controller_name' => 'AppController',
        ]);
    }

    #[Route('/login', name: 'page_login')]
    public function login(Request $request, TokenConnexionRepository $tokenRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->tokenConnexionService->getUserFromToken($request, $tokenRepository, $entityManager);
        if ($user) {
            return $this->render('accueil.html.twig', [
                'user' => $user
            ]);
        }
        return $this->render('login.html.twig');  
    }

    #[Route('/register', name: 'page_register')]
    public function register(Request $request, TokenConnexionRepository $tokenRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->tokenConnexionService->getUserFromToken($request, $tokenRepository, $entityManager);
        if ($user) {
            return $this->render('accueil.html.twig', [
                'user' => $user
            ]);
        }
        return $this->render('register.html.twig');  
    }

    #[Route('/crypto/graph', name: 'page_graph')]
    public function graph(Request $request, TokenConnexionRepository $tokenRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->tokenConnexionService->getUserFromToken($request, $tokenRepository, $entityManager);
        if ($user) {
            return $this->render('graphe/graphe.html.twig', [
                'user' => $user
            ]);
        }
        return $this->render('graphe/graphe.html.twig');  
    }

    #[Route('/crypto/cours', name: 'page_cour_crypto')]
    public function cours_crypto(Request $request, TokenConnexionRepository $tokenRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->tokenConnexionService->getUserFromToken($request, $tokenRepository, $entityManager);
        if ($user) {
            return $this->render('cour_crypto/index.html.twig', [
                'user' => $user
            ]);
        }
        return $this->render('cour_crypto/index.html.twig');  
    }

    #[Route('/crypto/analyse', name: 'page_analyse_crypto'), ]
    public function getPage(Request $request, TokenConnexionRepository $tokenRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->tokenConnexionService->getUserFromToken($request, $tokenRepository, $entityManager);
        if ($user) {
            return $this->render('cour_crypto/analyse.html.twig', [
                'user' => $user
            ]);
        }
        return $this->render('cour_crypto/analyse.html.twig');
    }
}
