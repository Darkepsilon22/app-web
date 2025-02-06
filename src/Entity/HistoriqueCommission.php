<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

// Entité HistoriqueCommission
#[ORM\Entity]
#[ORM\Table(name: "historique_pourcentage_commission")]
class HistoriqueCommission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id_historique_commission", type: "integer")]
    private int $idHistoriqueCommission;

    #[ORM\Column(name: "date_historique_porucentage", type: "datetime")]
    private \DateTimeInterface $dateHistoriquePourcentage;

    #[ORM\Column(name: "valeur_historique_pourcentage", type: "decimal", precision: 15, scale: 2)]
    private float $valeurHistoriquePourcentage;

    // Getters et Setters
    public function getIdHistoriqueCommission(): int
    {
        return $this->idHistoriqueCommission;
    }

    public function getDateHistoriquePourcentage(): \DateTimeInterface
    {
        return $this->dateHistoriquePourcentage;
    }

    public function setDateHistoriquePourcentage(\DateTimeInterface $dateHistoriquePourcentage): void
    {
        $this->dateHistoriquePourcentage = $dateHistoriquePourcentage;
    }

    public function getValeurHistoriquePourcentage(): float
    {
        return $this->valeurHistoriquePourcentage;
    }

    public function setValeurHistoriquePourcentage(float $valeurHistoriquePourcentage): void
    {
        $this->valeurHistoriquePourcentage = $valeurHistoriquePourcentage;
    }
}
