<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_NUMBER', fields: ['numero'])]
class Client {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'client', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    private ?string $numero = null;

    /**
     * @var Collection<int, Compte>
     */
    #[ORM\OneToMany(targetEntity: Compte::class, mappedBy: 'owner')]
    private Collection $accounts;

    public function __construct() {
        $this->accounts = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getUser(): ?User {
        return $this->user;
    }

    public function setUser(User $user): static {
        $this->user = $user;

        return $this;
    }

    public function getIdentity(): string {
        return $this->user->getIdentity() . " (" . $this->getNumero() . ")";
    }

    public function getNumero(): ?string {
        return $this->numero;
    }

    public function setNumero(string $numero): static {
        if (!preg_match('^\\d{10}$', $numero))
            throw new InvalidArgumentException('Le numéro de compte doit se composer de dix chiffres');
        $this->numero = $numero;

        return $this;
    }

    /**
     * @return Collection<int, Compte>
     */
    public function getAccounts(): Collection {
        return $this->accounts;
    }

    public function addAccount(Compte $account): static {
        if (!$this->accounts->contains($account)) {
            $this->accounts->add($account);
            $account->setOwner($this);
        }

        return $this;
    }

    public function removeAccount(Compte $account): static {
        if ($this->accounts->removeElement($account)) {
            // set the owning side to null (unless already changed)
            if ($account->getOwner() === $this) {
                $account->setOwner(null);
            }
        }

        return $this;
    }

}
