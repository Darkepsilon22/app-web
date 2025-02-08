<?php
// src/Controller/LoginController.php
namespace App\Controller;

use App\Service\LoginService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpClient\HttpClient;


class LoginController extends AbstractController
{
    private $loginService;

    public function __construct(LoginService $loginService)
    {
        $this->loginService = $loginService;
    }

    #[Route('/login/send', name: 'app_login_send', methods: ['POST'])]
public function sendPin(Request $request): Response
{
    $email = $request->request->get('email');
    $password = $request->request->get('password');
    
    try {
        $response = $this->loginService->sendPin($email, $password);

        $successMessage = "Connexion réussie ! Un code PIN a été envoyé à votre email.";
        return $this->render('check_pin.html.twig', [
            'email' => $email,
            'password' => $password,
            'response' => $response,
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
public function verifyPin(Request $request): Response
{
    $email = $request->request->get('email');
    $password = $request->request->get('password');
    $pin = $request->request->get('pin');

    try {
        $response = $this->loginService->verifyPin($email, $password, $pin);

        return $this->render('accueil.html.twig');
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
public function resetSend(Request $request): Response
{
    if ($request->isMethod('POST')) {
        $email = $request->request->get('email');
        $password = $request->request->get('password');

        try {
            $response = $this->loginService->resetSend($email, $password);
    
            $message = 'Le lien de réinitialisation a été envoyé a votre mail.';
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

    return $this->render('reset_password.html.twig');
}




}


