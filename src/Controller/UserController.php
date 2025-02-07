<?php

// src/Controller/UserController.php

namespace App\Controller;

use App\Entity\Users;
use App\Entity\MouvementSolde;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class UserController extends AbstractController
{
    #[Route('/user/depot_retrait/{id}', name: 'user_depot_retrait', methods: ['GET', 'POST'])]
    public function depotRetrait(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Recherche de l'utilisateur dans la base de données
        $userFromDb = $entityManager->getRepository(Users::class)->find($id);

        // Si l'utilisateur n'existe pas, lancer une exception
        if (!$userFromDb) {
            throw new \Exception("Utilisateur non trouvé dans la base de données.");
        }

        if (!$userFromDb instanceof Users) {
            throw new \Exception("L'utilisateur connecté n'est pas valide.");
        }

        // Si la méthode est POST, traiter le formulaire
        if ($request->isMethod('POST')) {
            $somme = $request->request->get('somme');
            $operation = $request->request->get('operation');

            // Vérification de la somme
            if (empty($somme) || !is_numeric($somme)) {
                $this->addFlash('error', 'Le montant doit être un nombre valide.');
                return $this->redirectToRoute('user_depot_retrait', ['id' => $id]);
            }

            // Création du mouvement de solde
            $mouvementSolde = new MouvementSolde();
            $mouvementSolde->setSomme((string)$somme);  // Assurez-vous que la somme est convertie en string
            $mouvementSolde->setEstDepot($operation == 'depot');
            $mouvementSolde->setUser($userFromDb);  // Associer l'utilisateur
            $mouvementSolde->setDateMouvement(new \DateTimeImmutable());
            $mouvementSolde->setStatut('en_attente');  // Statut en attente de validation

            // Sauvegarde dans la base de données
            $entityManager->persist($mouvementSolde);
            $entityManager->flush();

            // Message de confirmation
            $this->addFlash('success', 'Votre demande de dépôt/retrait a été enregistrée et est en attente de validation.');

            return $this->redirectToRoute('user_depot_retrait', ['id' => $id]); // Redirection après l'enregistrement
        }

        // Si la méthode est GET, afficher le formulaire
        return $this->render('user/depot_retrait.html.twig', [
            'user' => $userFromDb,  // Vous pouvez envoyer l'utilisateur à la vue si nécessaire
        ]);
    }
}
