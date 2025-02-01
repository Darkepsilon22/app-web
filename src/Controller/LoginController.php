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

    // Route pour afficher le formulaire de login
    #[Route('/', name: 'app_login')]
    public function login(): Response
    {
        return $this->render('login.html.twig');  // Pass the 'error' variable as null
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

        // Si le PIN est valide, rediriger vers la page d'accueil
        return $this->render('accueil.html.twig');
    } catch (\Exception $e) {
        // Capturer l'exception et définir un message d'erreur
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
        // Récupérer l'email et le mot de passe du formulaire
        $email = $request->request->get('email');
        $password = $request->request->get('password');

        // Vérifier que l'email et le mot de passe sont valides
        if (!$email || !$password) {
            $this->addFlash('error', 'Veuillez entrer votre email et mot de passe.');
            return $this->redirectToRoute('app_reset_send');
        }

        // Préparer les données pour l'API
        $data = [
            'email' => $email,
            'password' => $password,
        ];

        // Envoi de la requête POST à l'API avec les données
        $client = HttpClient::create();
        try {
            $response = $client->request('POST', 'http://localhost:8080/api/user/resettentative/send', [
                'json' => $data
            ]);

            // Vérification de la réponse de l'API
            if ($response->getStatusCode() === 200) {
                $this->addFlash('success', 'Le lien de réinitialisation a été envoyé.');
            } else {
                $this->addFlash('error', 'Échec de l\'envoi du lien de réinitialisation.');
            }
        } catch (\Exception $e) {
            $this->addFlash('error', 'Une erreur est survenue : ' . $e->getMessage());
        }

        // Redirection vers le même formulaire avec les messages flash
        return $this->redirectToRoute('app_reset_send');
    }

    return $this->render('reset_password.html.twig');
}




}


