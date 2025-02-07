<?php
namespace App\Entity;

use App\Repository\MouvementCryptoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MouvementCryptoRepository::class)]
#[ORM\Table(name: "mouvement_crypto")]
class MouvementCrypto
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id_mouvement_crypto", type: "integer")]
    private ?int $id_mouvement_crypto = null;

    #[ORM\Column(name: "est_achat", type: "boolean")]
    private bool $estAchat;

    #[ORM\ManyToOne(targetEntity: Crypto::class)]
    #[ORM\JoinColumn(name: "id_crypto", referencedColumnName: "id_crypto", nullable: false)]
    private Crypto $crypto;

    #[ORM\ManyToOne(targetEntity: Users::class)]
    #[ORM\JoinColumn(name: "id_users", referencedColumnName: "id_users", nullable: false)]
    private Users $user;

    #[ORM\Column(name: "date_mouvement", type: "datetime")]
    private \DateTime $dateMouvement;

    #[ORM\Column(name: "quantite", type: "decimal", precision: 15, scale: 8)]
    private float $quantite;

    #[ORM\Column(name: "valeur_crypto", type: "decimal", precision: 15, scale: 2)]
    private float $valeurCrypto;

    
    public function getIdMouvementCrypto(): ?int { return $this->id_mouvement_crypto; }

    public function isEstAchat(): bool { return $this->estAchat; }
    public function setEstAchat(bool $estAchat): self { $this->estAchat = $estAchat; return $this; }

    public function getCrypto(): Crypto { return $this->crypto; }
    public function setCrypto(Crypto $crypto): self { $this->crypto = $crypto; return $this; }

    public function getUser(): Users { return $this->user; }
    public function setUser(Users $user): self { $this->user = $user; return $this; }

    public function getDateMouvement(): \DateTime { return $this->dateMouvement; }
    public function setDateMouvement(\DateTime $dateMouvement): self { $this->dateMouvement = $dateMouvement; return $this; }

    public function getQuantite(): float { return $this->quantite; }
    public function setQuantite(float $quantite): self { $this->quantite = $quantite; return $this; }

    /**
     * Get the value of valeurCrypto
     */ 
    public function getValeurCrypto()
    {
        return $this->valeurCrypto;
    }

    /**
     * Set the value of valeurCrypto
     *
     * @return  self
     */ 
    public function setValeurCrypto($valeurCrypto)
    {
        $this->valeurCrypto = $valeurCrypto;

        return $this;
    }
}
