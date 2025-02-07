<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "mouvement_solde")]
class MouvementSolde
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id_mouvement_solde", type: "integer")]
    private ?int $idMouvementSolde = null;

    #[ORM\Column(type: "decimal", precision: 19, scale: 5)]  // Changer à NUMERIC(19,5)
    private string $somme;

    #[ORM\Column(name: "date_mouvement", type: "datetime")]
    private \DateTimeInterface $dateMouvement;  // Remplacer "datetime_immutable" par "datetime" si nécessaire

    #[ORM\Column(name: "est_depot", type: "boolean")]
    private bool $estDepot;

    #[ORM\Column(type: "string", length: 255, options: ["default" => "en_attente"])]
    private string $statut;  // Nouveau champ statut

    #[ORM\ManyToOne(targetEntity: Users::class)]
    #[ORM\JoinColumn(name: "id_users", referencedColumnName: "id_users", nullable: false)]
    private Users $user;

    // Getters et Setters
    public function getIdMouvementSolde(): ?int { return $this->idMouvementSolde; }

    public function getSomme(): string { return $this->somme; }
    public function setSomme(string $somme): self { 
        $this->somme = $somme; 
        return $this; 
    }

    public function getDateMouvement(): \DateTimeInterface { return $this->dateMouvement; }
    public function setDateMouvement(\DateTimeInterface $dateMouvement): self {
        $this->dateMouvement = $dateMouvement;
        return $this;
    }

    public function isEstDepot(): bool { return $this->estDepot; }
    public function setEstDepot(bool $estDepot): self { 
        $this->estDepot = $estDepot; 
        return $this; 
    }

    public function getStatut(): string { return $this->statut; }
    public function setStatut(string $statut): self { 
        $this->statut = $statut; 
        return $this; 
    }

    public function getUser(): Users { return $this->user; }
    public function setUser(Users $user): self { 
        $this->user = $user; 
        return $this; 
    }
}
