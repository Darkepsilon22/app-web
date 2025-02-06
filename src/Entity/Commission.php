<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "commission")]
class Commission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id_commission;

    #[ORM\Column(type: "decimal", precision: 15, scale: 2)]
    private float $pourcentage;

    #[ORM\Column(type: "decimal", precision: 15, scale: 2)]
    private float $valeur;

    #[ORM\ManyToOne(targetEntity: MouvementCrypto::class)]
    #[ORM\JoinColumn(name: "id_mouvement_crypto", referencedColumnName: "id_mouvement_crypto", nullable: false)]
    private MouvementCrypto $mouvementCrypto;

    // Getters et Setters
    public function getIdCommission(): int
    {
        return $this->id_commission;
    }

    public function getPourcentage(): float
    {
        return $this->pourcentage;
    }

    public function setPourcentage(float $pourcentage): void
    {
        $this->pourcentage = $pourcentage;
    }

    public function getValeur(): float
    {
        return $this->valeur;
    }

    public function setValeur(float $valeur): void
    {
        $this->valeur = $valeur;
    }

    public function getMouvementCrypto(): MouvementCrypto
    {
        return $this->mouvementCrypto;
    }

    public function setMouvementCrypto(MouvementCrypto $mouvementCrypto): void
    {
        $this->mouvementCrypto = $mouvementCrypto;
    }
}
