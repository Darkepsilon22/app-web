<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PortefeuilleController extends AbstractController
{
    #[Route('/portefeuille', name: 'app_portefeuille')]
    public function index(): Response
    {
        return $this->render('index.html.twig');
    }
}
