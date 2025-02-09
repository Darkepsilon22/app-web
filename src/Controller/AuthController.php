<?php

// src/Controller/AuthController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Users;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthController extends AbstractController
{
    private $client;
    private $entityManager;
    private $passwordHasher;

    public function __construct(HttpClientInterface $client, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $this->client = $client;
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    #[Route('/register', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(Request $request, RequestStack $requestStack): Response
    {
        $session = $requestStack->getSession();
    
        if (!$session instanceof Session) {
            throw new \LogicException('La session est invalide.');
        }
    
        if ($request->isMethod('POST')) {
            $data = [
                'nom' => $request->request->get('nom'),
                'prenom' => $request->request->get('prenom'),
                'email' => $request->request->get('email'),
                'dateNaissance' => $request->request->get('dateNaissance'),
                'password' => $request->request->get('password'),
            ];
    
            // Envoi des données à l'API Identity Provider
            $response = $this->client->request('POST', 'http://localhost:8080/api/auth/register', [
                'json' => $data,
            ]);
    
            if ($response->getStatusCode() === 200) {
                // Enregistrement local dans la base crypto
                $this->saveToCryptoDatabase($data);
                
                $session->getFlashBag()->add('success', 'Un email de validation a été envoyé.');
            } else {
                $session->getFlashBag()->add('error', 'Une erreur s\'est produite.');
            }
    
            return $this->redirectToRoute('app_register');
        }
    
        return $this->render('register.html.twig', [
            'flashMessages' => json_encode($session->getFlashBag()->all()),
        ]);
    }

    private function saveToCryptoDatabase(array $userData)
    {
        try {
            // Vérifier si l'utilisateur existe déjà
            $existingUser = $this->entityManager->getRepository(Users::class)->findOneBy(['email' => $userData['email']]);

            if (!$existingUser) {
                $user = new Users();
                $user->setNom($userData['nom']);
                $user->setPrenom($userData['prenom']);
                $user->setEmail($userData['email']);
                $user->setDateNaissance(new \DateTime($userData['dateNaissance']));
                
                // Hash du mot de passe
                $hashedPassword = $this->passwordHasher->hashPassword($user, $userData['password']);
                $user->setPassword($hashedPassword);

                // Initialiser le solde à 0.00
                $user->setSolde("0.00");

                // Date d'inscription
                $user->setDateInscription(new \DateTimeImmutable());

                $this->entityManager->persist($user);
                $this->entityManager->flush();
            }
        } catch (\Exception $e) {
            error_log('Erreur lors de l\'enregistrement dans la base crypto : ' . $e->getMessage());
        }
    }

    #[Route('/verify', name: 'app_verify', methods: ['GET'])]
    public function verifyEmail(Request $request): Response
    {
        $token = $request->query->get('token');

        if ($token) {
            $response = $this->client->request('GET', 'http://localhost:8080/api/auth/verify', [
                'query' => ['token' => $token]
            ]);

            if ($response->getStatusCode() === 200) {
                return $this->render('verify_success.html.twig', ['message' => 'Votre compte a été validé avec succès.']);
            } else {
                return $this->render('verify_failure.html.twig', ['message' => 'Erreur lors de la validation du token.']);
            }
        }

        return $this->render('verify_failure.html.twig', ['message' => 'Aucun token trouvé.']);
    }
}
