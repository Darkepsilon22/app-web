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

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/modifier_commission', name: 'modifier_commission', methods: ['GET', 'POST'])]
    public function modifierCommission(Request $request): Response
    {
        $historiqueCommission = $this->entityManager
            ->getRepository(HistoriqueCommission::class)
            ->findOneBy([], ['dateHistoriquePourcentage' => 'DESC']);

        // Current date for the date input field
        $currentDate = new \DateTime();

        if ($request->isMethod('POST')) {
            $valeurPourcentage = $request->request->get('valeur_historique_pourcentage');
            $datePourcentage = $request->request->get('date_historique_pourcentage');

            if ($valeurPourcentage) {
                $nouvelleCommission = new HistoriqueCommission();
                $nouvelleCommission->setValeurHistoriquePourcentage((float)$valeurPourcentage);
                $nouvelleCommission->setDateHistoriquePourcentage(new \DateTime($datePourcentage));

                $this->entityManager->persist($nouvelleCommission);
                $this->entityManager->flush();

                $successMessage = "La nouvelle commission a été ajoutée avec succès.";
                return $this->render('commission/modifier_commission.html.twig', [
                    'success' => $successMessage,
                    'currentDate' => $currentDate,
                    'historiqueCommission' => $nouvelleCommission
                ]);
            }
        }

        return $this->render('commission/modifier_commission.html.twig', [
            'historiqueCommission' => $historiqueCommission,
            'currentDate' => $currentDate,
        ]);
    }
}

