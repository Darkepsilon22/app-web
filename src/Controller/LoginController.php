<?php
// src/Controller/LoginController.php
namespace App\Controller;

use App\Entity\TokenConnexion;
use App\Entity\Users;
use App\Repository\TokenConnexionRepository;
use App\Service\LoginService;
use App\Service\TokenConnexionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class LoginController extends AbstractController
{
    private $loginService;
    private $tokenConnexionService;

    public function __construct(LoginService $loginService, TokenConnexionService $tokenConnexionService)
    {
        $this->loginService = $loginService;
        $this->tokenConnexionService = $tokenConnexionService;
    }

#[Route('/login/send', name: 'app_login_send', methods: ['POST'])]
public function sendPin(Request $request): Response
{
    $email = $request->request->get('email');
    $password = $request->request->get('password');
    
    try {
        $response = $this->loginService->sendPin($email, $password);

        $successMessage = "Un code PIN a été envoyé à votre email.";
        return $this->render('check_pin.html.twig', [
            'email' => $email,
            'password' => $password,
            'success' => $successMessage,
        ]);
    } catch (\Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface $e) {
        $message = $e->getResponse()->getContent(false);
        return $this->render('login.html.twig', [
            'email' => $email,
            'error' => $message,
        ]);
    }
}

#[Route('/login/verify', name: 'app_login_verify', methods: ['POST'])]
public function verifyPin(Request $request, EntityManagerInterface $entityManager): Response
{
    $email = $request->request->get('email');
    $password = $request->request->get('password');
    $pin = $request->request->get('pin');

    try {
        $token = $this->loginService->verifyPin($email, $password, $pin);

        if (!$token) {
            return $this->render('login.html.twig', [
                'error' => 'Échec de la récupération du token'
            ]);
        }

        $user = $entityManager->getRepository(Users::class)->findOneBy(['email' => $email]);

        if (!$user) {
            return $this->render('login.html.twig', [
                'error' => 'Utilisateur introuvable.'
            ]);
        }

        $tokenRepository = $entityManager->getRepository(TokenConnexion::class);
        $existingToken = $tokenRepository->findOneBy(['user' => $user]);
        $expirationTimestamp = (new \DateTime())->modify('+1 hour');

        if ($existingToken) {
            $existingToken->setCode($token);
            $existingToken->setDateExpiration($expirationTimestamp);
            
        } else {
            $tokenEntity = new TokenConnexion();
            $tokenEntity->setCode($token);
            $tokenEntity->setDateExpiration($expirationTimestamp);
            $tokenEntity->setUser($user);
            $entityManager->persist($tokenEntity);
        }

        $entityManager->flush();

        $savedToken = $entityManager->getRepository(TokenConnexion::class)->findOneBy(['user' => $user]);
        if (!$savedToken) {
            return $this->render('login.html.twig', [
                'error' => 'Le token n\'a pas été enregistré en base.'
            ]);
        }

        $response = $this->render('accueil.html.twig');

        $cookie = new Cookie(
            'auth_token',    
            $token,          
            $expirationTimestamp->getTimestamp(),
            '/',             
            null,            
            false,           
            true             
        );
        $response->headers->setCookie($cookie);

        return $response;

    } catch (\Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface $e) {
        $message = $e->getResponse()->getContent(false);
        return $this->render('check_pin.html.twig', [
            'email' => $email,
            'password' => $password,
            'error' => $message,
        ]);
    }
}

#[Route('/user/resettentative/send', name: 'app_reset_send', methods: ['GET', 'POST'])]
public function resetSend(Request $request, TokenConnexionRepository $tokenRepository, EntityManagerInterface $entityManager): Response
{
    if ($request->isMethod('POST')) {
        $email = $request->request->get('email');
        $password = $request->request->get('password');

        try {
            $response = $this->loginService->resetSend($email, $password);
    
            $message = 'Un email de réinitialisation a été envoyé.';
            return $this->render('login.html.twig', [
                'success' => $message,
            ]);
        } catch (\Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface $e) {
            $message = $e->getResponse()->getContent(false);
            return $this->render('reset_password.html.twig', [
                'error' => $message,
            ]);
        }
    }

    $user = $this->tokenConnexionService->getUserFromToken($request, $tokenRepository, $entityManager);
    if ($user) {
        return $this->render('accueil.html.twig', [
            'user' => $user
        ]);
    }
    return $this->render('reset_password.html.twig');
}
}


