<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

// Entité HistoriqueCommission
#[ORM\Entity]
#[ORM\Table(name: "historique_commission")]
class HistoriqueCommission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id_historique_commission;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $date_historique;

    #[ORM\Column(type: "decimal", precision: 15, scale: 2)]
    private float $valeur_historique;

    // Getters et Setters
    public function getIdHistoriqueCommission(): int
    {
        return $this->id_historique_commission;
    }

    public function getDateHistorique(): \DateTimeInterface
    {
        return $this->date_historique;
    }

    public function setDateHistorique(\DateTimeInterface $date_historique): void
    {
        $this->date_historique = $date_historique;
    }

    public function getValeurHistorique(): float
    {
        return $this->valeur_historique;
    }

    public function setValeurHistorique(float $valeur_historique): void
    {
        $this->valeur_historique = $valeur_historique;
    }
}