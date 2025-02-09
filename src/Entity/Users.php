<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;



#[ORM\Entity]
#[ORM\Table(name: "users")]
class Users implements PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id_users", type: "integer")]
    private ?int $id_users = null;

    #[ORM\Column(type: "string", length: 255)]
    private string $prenom;

    #[ORM\Column(type: "string", length: 255)]
    private string $nom;

    #[ORM\Column(name: "date_naissance", type: "date")]
    private \DateTimeInterface $dateNaissance;

    #[ORM\Column(type: "string", length: 255, unique: true)]
    private string $email;

    #[ORM\Column(type: "string", length: 255)]
    private string $password;

    #[ORM\Column(type: "decimal", precision: 15, scale: 2, nullable: true)]
    private ?string $solde = "0.00";

    #[ORM\Column(name: "date_inscription", type: "datetime_immutable")]
    private \DateTimeInterface $dateInscription;

    public function getIdUsers(): ?int { return $this->id_users; }

    public function getPrenom(): string { return $this->prenom; }
    public function setPrenom(string $prenom): self { $this->prenom = $prenom; return $this; }

    public function getNom(): string { return $this->nom; }
    public function setNom(string $nom): self { $this->nom = $nom; return $this; }

    public function getDateNaissance(): \DateTimeInterface { return $this->dateNaissance; }
    public function setDateNaissance(\DateTimeInterface $dateNaissance): self { 
        $this->dateNaissance = $dateNaissance; 
        return $this; 
    }

    public function getEmail(): string { return $this->email; }
    public function setEmail(string $email): self { $this->email = $email; return $this; }

    public function setPassword(string $password): self { $this->password = $password; return $this; }

    public function getSolde(): ?string { return $this->solde; }
    public function setSolde(?string $solde): self { $this->solde = $solde; return $this; }

    public function getDateInscription(): \DateTimeInterface { return $this->dateInscription; }
    public function setDateInscription(\DateTimeInterface $dateInscription): self { 
        $this->dateInscription = $dateInscription; 
        return $this; 
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}
