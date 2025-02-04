<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "crypto")]
class Crypto
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer", name: "id_crypto")]
    private ?int $id_crypto = null;

    #[ORM\Column(type: "string", length: 50, nullable: false)]
    private string $intitule;

    #[ORM\Column(type: "decimal", precision: 15, scale: 2, nullable: true)]
    private ?string $currentValeur = null;

    public function __construct(string $intitule, ?string $currentValeur = null)
    {
        $this->intitule = $intitule;
        $this->currentValeur = $currentValeur;
    }

    public function getIdCrypto(): ?int
    {
        return $this->id_crypto;
    }

    public function getIntitule(): string
    {
        return $this->intitule;
    }

    public function setIntitule(string $intitule): self
    {
        $this->intitule = $intitule;
        return $this;
    }

    public function getCurrentValeur(): ?string
    {
        return $this->currentValeur;
    }

    public function setCurrentValeur(?string $currentValeur): self
    {
        $this->currentValeur = $currentValeur;
        return $this;
    }
}
