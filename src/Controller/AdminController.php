<?php
// src/Controller/AdminController.php
namespace App\Controller;

use App\Entity\UsersAdmin;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;


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

            if ($user && password_verify($password, $user->getPassword())) {
                $session->set('user', $username); 
                return $this->redirectToRoute('admin_dashboard'); 
            } else {
                $this->addFlash('error', 'Identifiant incorrect');
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
}
?>
