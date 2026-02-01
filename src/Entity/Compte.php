<?php

namespace App\Entity;

use App\Repository\CompteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompteRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_NUMBER', fields: ['numero'])]
class Compte {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'accounts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $owner = null;

    #[ORM\Column(length: 255)]
    private ?string $numero = null;

    #[ORM\Column]
    private ?float $solde = null;

    #[ORM\Column]
    private ?bool $decouvert = null;

    #[ORM\Column(nullable: true)]
    private ?float $decouvertAutorise = null;

    public function getId(): ?int {
        return $this->id;
    }

    public function getOwner(): ?Client {
        return $this->owner;
    }

    public function setOwner(?Client $owner): static {
        $this->owner = $owner;

        return $this;
    }

    public function getNumero(): ?string {
        return $this->numero;
    }

    public function setNumero(string $numero): static {
        $this->numero = $numero;

        return $this;
    }

    public function getSolde(): ?float {
        return $this->solde;
    }

    public function setSolde(float $solde): static {
        $this->solde = $solde;

        return $this;
    }

    public function isDecouvert(): ?bool {
        return $this->decouvert;
    }

    public function setDecouvert(bool $decouvert): static {
        $this->decouvert = $decouvert;

        return $this;
    }

    public function getDecouvertAutorise(): ?float {
        return $this->decouvertAutorise;
    }

    public function setDecouvertAutorise(?float $decouvertAutorise): static {
        $this->decouvertAutorise = $decouvertAutorise;

        return $this;
    }
}
