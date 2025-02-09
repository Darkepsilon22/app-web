<?php

namespace App\Controller;

use App\Service\LoginService;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Users;
use App\Entity\TokenConnexion;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractController
{
    private $loginService;
    private $entityManager;

    public function __construct(LoginService $loginService, EntityManagerInterface $entityManager)
    {
        $this->loginService = $loginService;
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'app_login')]
    public function login(): Response
    {
        return $this->render('login.html.twig');
    }

    #[Route('/login/send', name: 'app_login_send', methods: ['POST'])]
    public function sendPin(Request $request): Response
    {
        $email = $request->request->get('email');
        $password = $request->request->get('password');

        try {
            $response = $this->loginService->sendPin($email, $password);

            return $this->render('check_pin.html.twig', [
                'email' => $email,
                'password' => $password,
                'response' => $response,
                'success' => "Connexion réussie ! Un code PIN a été envoyé à votre email.",
            ]);
        } catch (\Exception $e) {
            return $this->render('login.html.twig', [
                'email' => $email,
                'error' => "Email ou mot de passe incorrect.",
            ]);
        }
    }

    #[Route('/login/verify', name: 'app_login_verify', methods: ['POST'])]
    public function verifyPin(Request $request): Response
    {
        $email = $request->request->get('email');
        $password = $request->request->get('password');
        $pin = $request->request->get('pin');
    
        try {
            $token = $this->loginService->verifyPin($email, $password, $pin);
    
            if (!$token) {
                throw new \Exception("Échec de la récupération du token.");
            }
    
            $user = $this->entityManager->getRepository(Users::class)->findOneBy(['email' => $email]);
    
            if (!$user) {
                throw new \Exception("Utilisateur introuvable.");
            }
    
            $tokenRepository = $this->entityManager->getRepository(TokenConnexion::class);
            $existingToken = $tokenRepository->findOneBy(['user' => $user]);
    
            if ($existingToken) {
                $existingToken->setCode($token);
                $existingToken->setDateExpiration((new \DateTime())->modify('+1 hour'));
            } else {
                $tokenEntity = new TokenConnexion();
                $tokenEntity->setCode($token);
                $tokenEntity->setDateExpiration((new \DateTime())->modify('+1 hour'));
                $tokenEntity->setUser($user);
                $this->entityManager->persist($tokenEntity);
            }
    
            $this->entityManager->flush();
    
            $savedToken = $this->entityManager->getRepository(TokenConnexion::class)->findOneBy(['user' => $user]);
            if (!$savedToken) {
                throw new \Exception("Le token n'a pas été enregistré en base.");
            }
    
            return $this->redirectToRoute('app_portefeuille');
    
        } catch (\Exception $e) {
            // dump("Erreur: " . $e->getMessage()); 
            return $this->render('check_pin.html.twig', [
                'email' => $email,
                'password' => $password,
                'error' => "PIN invalide ou délai dépassé. " . $e->getMessage(),
            ]);
        }
    }

    #[Route('/portefeuille', name: 'app_portefeuille')]
public function portefeuille(): Response
{
    return $this->render('accueil.html.twig');
}

    
    #[Route('/user/resettentative/send', name: 'app_reset_send', methods: ['GET', 'POST'])]
    public function resetSend(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $password = $request->request->get('password');

            if (!$email || !$password) {
                $this->addFlash('error', 'Veuillez entrer votre email et mot de passe.');
                return $this->redirectToRoute('app_reset_send');
            }

            try {
                $this->loginService->resetSend($email, $password);
                $this->addFlash('success', 'Le lien de réinitialisation a été envoyé.');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue : ' . $e->getMessage());
            }

            return $this->redirectToRoute('app_reset_send');
        }

        return $this->render('reset_password.html.twig');
    }
}
