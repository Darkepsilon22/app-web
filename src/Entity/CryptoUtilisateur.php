<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "crypto_utilisateur")]
class CryptoUtilisateur
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer', name: 'Id_crypto_utilisateur')]
    private int $idCryptoUtilisateur;

    #[ORM\Column(type: 'decimal', precision: 15, scale: 2)]
    private float $quantite;

    #[ORM\ManyToOne(targetEntity: Users::class)]
    #[ORM\JoinColumn(name: 'id_users', referencedColumnName: 'id_users', nullable: false)]
    private Users $user;

    #[ORM\ManyToOne(targetEntity: Crypto::class)]
    #[ORM\JoinColumn(name: 'id_crypto', referencedColumnName: 'id_crypto', nullable: false)]
    private Crypto $crypto;

    public function getIdCryptoUtilisateur(): int
    {
        return $this->idCryptoUtilisateur;
    }

    public function setIdCryptoUtilisateur(int $idCryptoUtilisateur): self
    {
        $this->idCryptoUtilisateur = $idCryptoUtilisateur;
        return $this;
    }

    public function getQuantite(): float
    {
        return $this->quantite;
    }

    public function setQuantite(float $quantite): self
    {
        $this->quantite = $quantite;
        return $this;
    }

    public function getUser(): Users
    {
        return $this->user;
    }

    public function setUser(Users $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getCrypto(): Crypto
    {
        return $this->crypto;
    }

    public function setCrypto(Crypto $crypto): self
    {
        $this->crypto = $crypto;
        return $this;
    }
}
