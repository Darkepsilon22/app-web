<?php
// src/Controller/AuthController.php
namespace App\Controller;

use App\Service\AuthService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;


class AuthController extends AbstractController
{
    private $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    #[Route('/register/send', name: 'send_register', methods: ['POST'])]
    public function register_send(Request $request): Response
    {
        $nom = $request->request->get('nom');
        $prenom = $request->request->get('prenom');
        $email = $request->request->get('email');
        $dateNaissance = $request->request->get('dateNaissance');
        $password = $request->request->get('password');

        try {
            $response = $this->authService->register($nom, $prenom, $email, $dateNaissance, $password);
    
            $message = 'Un email de validation a été envoyé.';
            return $this->render('register.html.twig', [
                'success' => $message,
            ]);
        } catch (\Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface $e) {
            $message = $e->getResponse()->getContent(false);
            return $this->render('register.html.twig', [
                'error' => $message,
            ]);
        }
    }
    
    // #[Route('/register', name: 'app_register', methods: ['GET', 'POST'])]
    // public function register(Request $request, HttpClientInterface $client, RequestStack $requestStack): Response
    // {
    //     $session = $requestStack->getSession();
    
    //     if (!$session instanceof Session) {
    //         throw new \LogicException('La session est invalide.');
    //     }
    
    //     if ($request->isMethod('POST')) {
    //         $data = [
    //             'nom' => $request->request->get('nom'),
    //             'prenom' => $request->request->get('prenom'),
    //             'email' => $request->request->get('email'),
    //             'dateNaissance' => $request->request->get('dateNaissance'),
    //             'password' => $request->request->get('password'),
    //         ];
    
    //         $response = $client->request('POST', 'http://localhost:8080/api/auth/register', [
    //             'json' => $data,
    //         ]);
    
    //         if ($response->getStatusCode() === 200) {
    //             $session->getFlashBag()->add('success', 'Un email de validation a été envoyé.');
    //         } else {
    //             $session->getFlashBag()->add('error', 'Une erreur s\'est produite.');
    //         }
    
    //         return $this->redirectToRoute('app_register');
    //     }
    
    //     return $this->render('register.html.twig', [
    //         'flashMessages' => json_encode($session->getFlashBag()->all()),
    //     ]);
    // }
    


    // #[Route('/verify', name: 'app_verify', methods: ['GET'])]
    // public function verifyEmail(Request $request): Response
    // {
    //     $token = $request->query->get('token');

    //     if ($token) {
    //         $response = $this->client->request('GET', 'http://localhost:8080/api/auth/verify?token=', [
    //             'query' => ['token' => $token]
    //         ]);

    //         $statusCode = $response->getStatusCode();

    //         if ($statusCode === 200) {
    //             return $this->render('verify_success.html.twig', ['message' => 'Votre compte a été validé avec succès.']);
    //         } else {
    //             return $this->render('verify_failure.html.twig', ['message' => 'Erreur lors de la validation du token.']);
    //         }
    //     }

    //     return $this->render('verify_failure.html.twig', ['message' => 'Aucun token trouvé.']);
    // }
}
?>