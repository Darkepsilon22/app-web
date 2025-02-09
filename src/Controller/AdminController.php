<?php
// src/Controller/AdminController.php
namespace App\Controller;

use App\Entity\UsersAdmin;
use App\Entity\MouvementSolde;
use App\Entity\Users;
use App\Repository\MouvementCryptoRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class AdminController extends AbstractController
{
    #[Route('/admin/login', name: 'admin_login', methods: ['GET', 'POST'])]
    public function login(Request $request, RequestStack $requestStack, EntityManagerInterface $entityManager): Response
    {
        $session = $requestStack->getSession();

        if ($request->isMethod('POST')) {
            $username = $request->request->get('username');
            $password = $request->request->get('password');

            // Vérifier si l'utilisateur existe dans la BDD
            $user = $entityManager->getRepository(UsersAdmin::class)->findOneBy(['username' => $username]);

            if ($user) {
                if(password_verify($password, $user->getPassword())){
                    $session->set('user', $username); 
                    return $this->redirectToRoute('admin_dashboard');
                } else {
                    return $this->render('admin/login.html.twig', ['error' => 'Mot de passe incorrect']);
                }
            } else {
                return $this->render('admin/login.html.twig', ['error' => 'Identifiant incorrect']);
            }
        }

        return $this->render('admin/login.html.twig');
    }

    #[Route('/admin/dashboard', name: 'admin_dashboard')]
    public function dashboard(RequestStack $requestStack): Response
    {
        $session = $requestStack->getSession();

        if ($session->get('user') !== 'admin') {
            return $this->redirectToRoute('admin_login');
        }

        return $this->render('admin/dashboard.html.twig', ['user' => 'admin']);
    }

    #[Route('/admin/logout', name: 'admin_logout')]
    public function logout(RequestStack $requestStack): Response
    {
        $session = $requestStack->getSession();
        $session->invalidate(); // Détruit la session
        return $this->redirectToRoute('admin_login');
    }

    #[Route('/admin/gestion_mouvements', name: 'admin_gestion_mouvements')]
    public function gestionMouvements(EntityManagerInterface $entityManager, RequestStack $requestStack): Response
    {
        $session = $requestStack->getSession();

        if ($session->get('user') !== 'admin') {
            return $this->redirectToRoute('admin_login');
        }

        // Récupérer tous les mouvements en attente
        $mouvements = $entityManager->getRepository(MouvementSolde::class)->findBy(['statut' => 'en_attente']);

        return $this->render('admin/gestion_mouvements.html.twig', [
            'mouvements' => $mouvements,
        ]);
    }

    #[Route('/admin/valider_mouvement/{id}', name: 'admin_valider_mouvement')]
    public function validerMouvement($id, EntityManagerInterface $entityManager): Response
    {
        $mouvement = $entityManager->getRepository(MouvementSolde::class)->find($id);

        if ($mouvement) {
            // Mettre à jour le statut du mouvement
            $mouvement->setStatut('valide');

            // Mettre à jour le solde de l'utilisateur
            $user = $mouvement->getUser();
            if ($mouvement->isEstDepot()) {
                $user->setSolde($user->getSolde() + $mouvement->getSomme());
            } else {
                $user->setSolde($user->getSolde() - $mouvement->getSomme());
            }

            $entityManager->flush();
            $this->addFlash('success', 'Mouvement validé et solde mis à jour.');
        } else {
            $this->addFlash('error', 'Mouvement introuvable.');
        }

        return $this->redirectToRoute('admin_gestion_mouvements');
    }

    #[Route('/admin/refuser_mouvement/{id}', name: 'admin_refuser_mouvement')]
    public function refuserMouvement($id, EntityManagerInterface $entityManager): Response
    {
        $mouvement = $entityManager->getRepository(MouvementSolde::class)->find($id);

        if ($mouvement) {
            // Mettre à jour le statut du mouvement
            $mouvement->setStatut('refuse');
            $entityManager->flush();
            $this->addFlash('success', 'Mouvement refusé.');
        } else {
            $this->addFlash('error', 'Mouvement introuvable.');
        }

        return $this->redirectToRoute('admin_gestion_mouvements');
    }

    #[Route('/admin/statusers', name: 'admin_statuser')]
    public function statuser(ManagerRegistry $doctrine,MouvementCryptoRepository $mouvementCryptoRepository, RequestStack $requestStack): Response {
        $session = $requestStack->getSession();

        if ($session->get('user') !== 'admin') {
            return $this->redirectToRoute('admin_login');
        }
        
        $users = $doctrine->getRepository(Users::class)->findAll();
        $data = [];

        foreach ($users as $user) {
            $userId = $user->getIdUsers();
            $totalAchat = $mouvementCryptoRepository->getTotalAchatByUser($userId);
            $totalVente = $mouvementCryptoRepository->getTotalVenteByUser($userId);

            $data[] = [
                'id' => $userId,
                'prenom' => $user->getPrenom(),
                'nom' => $user->getNom(),
                'email' => $user->getEmail(),
                'solde' => $user->getSolde(),
                'totalAchat' => $totalAchat,
                'totalVente' => $totalVente,
            ];
        }

        return $this->render('admin/statut_users.html.twig', [
            'usersData' => $data
        ]);
    }
}
?>
