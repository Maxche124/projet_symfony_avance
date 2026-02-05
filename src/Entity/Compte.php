<?php

namespace App\Entity;

use App\Exceptions\InvalidAmountFormat;
use App\Exceptions\InvalidStringFormat;
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
    private string $numeroRegex = '/^[A-Z]{2}[0-9]{10}$/';

    #[ORM\Column]
    private float $solde = 0;

    #[ORM\Column]
    private bool $decouvert = false;

    #[ORM\Column(nullable: true)]
    private float $decouvertAutorise = 0;

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

    /**
     * @throws InvalidStringFormat
     */
    public function setNumero(string $numero): static {
        if (!preg_match($this->numeroRegex, $numero))
            throw new InvalidStringFormat($numero, "Le numéro doit suivre le pattern $this->numeroRegex");
        $this->numero = $numero;

        return $this;
    }

    /**
     * Définis si le compte peut être en découvert (true) ou non
     * @param bool $decouvert
     * @return $this
     */
    public function setDecouvertStatut(bool $decouvert): static {
        $this->decouvert = $decouvert;
        return $this;
    }

    /**
     * @throws InvalidAmountFormat
     */
    public function setMontantDecouvert(float $decouvertAutorise): static {
        $decouvert = -abs($decouvertAutorise);
        if ($decouvert > $this->getSolde())
            throw new InvalidAmountFormat($decouvert, "Le nouveau découvert est incompatible avec le solde actuel");
        $this->decouvertAutorise = $decouvert;
        return $this;
    }

    public function getSolde(): float {
        return $this->solde;
    }

    public function setSolde(float $solde): static {
        $this->solde = $solde;

        return $this;
    }

    /**
     * @throws InvalidAmountFormat
     */
    public function crediter(float $montant): static {
        if ($montant <= 0)
            throw new InvalidAmountFormat($montant, "Le montant d'un crédit doit être strictement supérieur à 0");
        $this->solde += $montant;
        return $this;
    }

    /**
     * @throws InvalidAmountFormat
     */
    public function debiter(float $montant): static {
        if ($montant <= 0)
            throw new InvalidAmountFormat($montant, "Le montant d'un débit doit être strictement supérieur à 0");

        $newSolde = $this->getSolde() - $montant;
        if ($newSolde < $this->getMinimumSolde())
            throw new InvalidAmountFormat($montant, "Vous ne pouvez pas débiter cette somme (découvert autorisé: {$this->getMinimumSolde()})");

        $this->solde -= $montant;
        return $this;
    }

    public function getMinimumSolde(): float {
        return $this->getDecouvertStatus() ?
            $this->getMontantDecouvert() :
            0;
    }

    /**
     * Renvoie un booléen représentant l'autorisation de découvert
     * @return bool
     */
    public function getDecouvertStatus(): bool {
        return $this->decouvert;
    }

    public function getMontantDecouvert(): float {
        return $this->decouvertAutorise;
    }
}
