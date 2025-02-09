<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AppController extends AbstractController{
    #[Route('/', name: 'page_acceuil')]
    public function index(): Response
    {
        return $this->render('accueil.html.twig', [
            'controller_name' => 'AppController',
        ]);
    }

    #[Route('/login', name: 'page_login')]
    public function login(): Response
    {
        return $this->render('login.html.twig');  
    }

    #[Route('/register', name: 'page_register')]
    public function register(): Response
    {
        return $this->render('register.html.twig');  
    }

    #[Route('/crypto/graph', name: 'page_graph')]
    public function graph(): Response
    {
        return $this->render('graphe/graphe.html.twig');  
    }
}
