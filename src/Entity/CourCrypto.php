<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "cour_crypto")]
class CourCrypto
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id_cour_crypto;

    #[ORM\Column(type: "datetime")]
    private \DateTime $instant;

    #[ORM\Column(type: "decimal", precision: 19, scale: 5)]
    private string $valeur_dollar;

    #[ORM\ManyToOne(targetEntity: Crypto::class)]
    #[ORM\JoinColumn(name: "id_crypto", referencedColumnName: "id_crypto", nullable: false)]
    private Crypto $crypto;

    public function getIdCourCrypto(): int
    {
        return $this->id_cour_crypto;
    }

    public function getInstant(): \DateTime
    {
        return $this->instant;
    }

    public function setInstant(\DateTime $instant): self
    {
        $this->instant = $instant;
        return $this;
    }

    public function getValeurDollar(): string
    {
        return $this->valeur_dollar;
    }

    public function setValeurDollar(string $valeur_dollar): self
    {
        $this->valeur_dollar = $valeur_dollar;
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
