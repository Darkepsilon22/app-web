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
    } catch (\Exception $e) {
        $errorMessage = "Email ou mot de passe incorrect.";
        return $this->render('login.html.twig', [
            'email' => $email,
            'error' => $errorMessage,
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
    } catch (\Exception $e) {
        $errorMessage = "PIN invalide ou délai de 90 secondes dépassé.";
        return $this->render('check_pin.html.twig', [
            'email' => $email,
            'password' => $password,
            'error' => $errorMessage,
        ]);
    }
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

        $data = [
            'email' => $email,
            'password' => $password,
        ];

        $client = HttpClient::create();
        try {
            $response = $client->request('POST', 'http://localhost:8080/api/user/resettentative/send', [
                'json' => $data
            ]);

            if ($response->getStatusCode() === 200) {
                $this->addFlash('success', 'Le lien de réinitialisation a été envoyé.');
            } else {
                $this->addFlash('error', 'Échec de l\'envoi du lien de réinitialisation.');
            }
        } catch (\Exception $e) {
            $this->addFlash('error', 'Une erreur est survenue : ' . $e->getMessage());
        }

        return $this->redirectToRoute('app_reset_send');
    }

    return $this->render('reset_password.html.twig');
}




}


