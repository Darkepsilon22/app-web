<?php

// UserController.php

namespace App\Controller;

use App\Entity\Users;
use App\Entity\MouvementSolde;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;  // Correct import
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class UserController extends AbstractController
{
    #[Route('/user/depot_retrait/{id}', name: 'user_depot_retrait', methods: ['GET', 'POST'])]
    public function depotRetrait(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $userFromDb = $entityManager->getRepository(Users::class)->find($id);

        if (!$userFromDb) {
            throw new \Exception("Utilisateur non trouvé dans la base de données.");
        }

        if (!$userFromDb instanceof Users) {
            throw new \Exception("L'utilisateur connecté n'est pas valide.");
        }

        if ($request->isMethod('POST')) {
            $somme = $request->request->get('somme');
            $operation = $request->request->get('operation');

            if (empty($somme) || !is_numeric($somme)) {
                $this->addFlash('error', 'Le montant doit être un nombre valide.');
                return $this->redirectToRoute('user_depot_retrait', ['id' => $id]);
            }

            $mouvementSolde = new MouvementSolde();
            $mouvementSolde->setSomme((string)$somme); 
            $mouvementSolde->setEstDepot($operation == 'depot');
            $mouvementSolde->setUser($userFromDb);  
            $mouvementSolde->setDateMouvement(new \DateTimeImmutable());
            $mouvementSolde->setStatut('en_attente'); 

            $entityManager->persist($mouvementSolde);
            $entityManager->flush();

            // Renvoi de la réponse JSON
            return new JsonResponse([
                'status' => 'success',
                'message' => 'Votre demande de dépôt/retrait a été enregistrée et est en attente de validation.',
                'mouvement' => [
                    'id' => $mouvementSolde->getIdMouvementSolde(),
                    'somme' => $mouvementSolde->getSomme(),
                    'date_mouvement' => $mouvementSolde->getDateMouvement()->format('Y-m-d H:i:s'),
                    'est_depot' => $mouvementSolde->isEstDepot() ? 'Dépôt' : 'Retrait',
                    'statut' => $mouvementSolde->getStatut(),
                ]
            ]);
        }

        return $this->render('user/depot_retrait.html.twig', [
            'user' => $userFromDb,  
        ]);
    }
}
