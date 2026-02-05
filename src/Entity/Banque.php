<?php

namespace App\Entity;

use App\Repository\BanqueRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BanqueRepository::class)]
class Banque {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, Client>
     */
    #[ORM\OneToMany(targetEntity: Client::class, mappedBy: 'banque')]
    private Collection $clients;

    public function __construct() {
        $this->clients = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
    }

    /**
     * @return Collection<int, Client>
     */
    public function getClients(): Collection {
        return $this->clients;
    }

    public function addClient(Client $client): static {
        if (!$this->clients->contains($client)) {
            $this->clients->add($client);
            $client->setBanque($this);
        }

        return $this;
    }

    public function removeClient(Client $client): static {
        if ($this->clients->removeElement($client)) {
            if ($client->getBanque() === $this) {
                $client->setBanque(null);
            }
        }

        return $this;
    }

    public function addGestionnaire(User $user): static {
        $roles = $user->getRoles();
        if (!in_array('ROLE_GESTIONNAIRE', $roles)) {
            $roles[] = 'ROLE_GESTIONNAIRE';
            $user->setRoles($roles);
        }
        return $this;
    }

    public function removeGestionnaire(User $user): static {
        $roles = array_diff($user->getRoles(), ['ROLE_GESTIONNAIRE']);
        $user->setRoles($roles);
        return $this;
    }
}
