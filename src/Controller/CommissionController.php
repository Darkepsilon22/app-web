<?php

// src/Controller/CommissionController.php
namespace App\Controller;

use App\Entity\HistoriqueCommission;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class CommissionController extends AbstractController
{
    private $entityManager;

    // Constructeur avec injection de EntityManager
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/modifier_commission', name: 'modifier_commission', methods: ['GET', 'POST'])]
    public function modifierCommission(Request $request): Response
    {
        // Récupère la dernière commission (pour affichage seulement)
        $historiqueCommission = $this->entityManager
            ->getRepository(HistoriqueCommission::class)
            ->findOneBy([], ['dateHistoriquePourcentage' => 'DESC']);

        // Si le formulaire est soumis, on récupère les données
        if ($request->isMethod('POST')) {
            $valeurPourcentage = $request->request->get('valeur_historique_pourcentage');
            if ($valeurPourcentage) {
                // Créer une nouvelle commission avec un nouveau pourcentage
                $nouvelleCommission = new HistoriqueCommission();
                $nouvelleCommission->setValeurHistoriquePourcentage((float)$valeurPourcentage);

                // Mettre à jour la date de modification à la date actuelle
                $nouvelleCommission->setDateHistoriquePourcentage(new \DateTime());

                // Sauvegarde de la nouvelle commission dans l'historique
                $this->entityManager->persist($nouvelleCommission);
                $this->entityManager->flush();

                // Message de succès
                $this->addFlash('success', 'La nouvelle commission a été ajoutée avec succès.');

                // Redirection vers la page de modification
                return $this->redirectToRoute('modifier_commission');
            }
        }

        return $this->render('commission/modifier_commission.html.twig', [
            'historiqueCommission' => $historiqueCommission,
        ]);
    }
}
