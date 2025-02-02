<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "crypto")]
class Crypto
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id_crypto;

    #[ORM\Column(type: "string", length: 50)]
    private string $intitule;

    #[ORM\Column(type: "decimal", precision: 19, scale: 5, nullable: true)]
    private ?string $currentValeur = null;

    public function getIdCrypto(): int
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
