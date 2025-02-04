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
    private ?int $id_mouvement_solde = null;

    #[ORM\Column(type: "decimal", precision: 10, scale: 2)]
    private string $somme; 

    #[ORM\Column(name: "date_mouvement", type: "datetime_immutable")]
    private \DateTimeImmutable  $dateMouvement;

    #[ORM\Column(name: "est_depot", type: "boolean")]
    private bool $estDepot;

    #[ORM\ManyToOne(targetEntity: Users::class)]
    #[ORM\JoinColumn(name: "id_users", referencedColumnName: "id_users", nullable: false)]
    private Users $user;

    public function getIdMouvementSolde(): ?int { return $this->id_mouvement_solde; }

    public function getSomme(): string { return $this->somme; }
    public function setSomme(string $somme): self { $this->somme = $somme; return $this; }

    public function getDateMouvement(): \DateTimeInterface { return $this->dateMouvement; }
    public function setDateMouvement(\DateTimeImmutable $dateMouvement): self {
        $this->dateMouvement = $dateMouvement;
        return $this;
    }
    

    public function isEstDepot(): bool { return $this->estDepot; }
    public function setEstDepot(bool $estDepot): self { $this->estDepot = $estDepot; return $this; }

    public function getUser(): Users { return $this->user; }
    public function setUser(Users $user): self { $this->user = $user; return $this; }
}
