<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface {
    public const GENDER_MALE = "Homme";
    public const GENDER_FEMALE = "Femme";
    public const GENDER_OTHER = "Autre/Non communiqué";

    public const GENDERS = [
        self::GENDER_MALE,
        self::GENDER_FEMALE,
        self::GENDER_OTHER
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Assert\Email(
        message: "L'adresse {{ value }} n'est pas un email valide.",
    )]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var ?string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\Column(length: 255)]
    private ?string $adresse = null;

    #[ORM\Column(length: 10)]
    #[Assert\Choice(choices: self::GENDERS)]
    private string $gender = self::GENDER_OTHER;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?Client $client = null;

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string {
        return (string)$this->email;
    }

    /**
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array {
        $data = (array)$this;
        $data["\0" . self::class . "\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    public function eraseCredentials(): void {
    }

    public function toString(): string {
        return "Utilisateur " . $this->getId() .
            " [Roles: " . $this->getRoleString() .
            ", nom : " . $this->getFirstName() .
            ", prenom : " . $this->getLastName() .
            ", adresse : " . $this->getAdresse() .
            ", email : " . $this->getEmail() .
            ", genre : " . $this->getGender() .
            ", password : " . $this->getPassword() . "]";
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getRoleString(): string {
        //Avec une version plus récente, on pourrait utiliser directement un enum mais en v7 c'est plus simple de faire ça manuellement
        $output = [];
        foreach ($this->roles as $role) {
            switch ($role) {
                case "ROLE_USER":
                    $output[] = "Utilisateur";
                    break;

                case "ROLE_MANAGER":
                    $output[] = "Gestionnaire";
                    break;
            }
        }

        return implode(", ", $output);
    }

    public function getFirstName(): ?string {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static {
        $this->lastName = $lastName;

        return $this;
    }

    public function getAdresse(): ?string {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static {
        $this->adresse = $adresse;

        return $this;
    }

    public function getEmail(): ?string {
        return $this->email;
    }

    public function setEmail(string $email): static {
        $this->email = $email;

        return $this;
    }

    public function getGender(): string {
        return $this->gender;
    }

    public function setGender(string $gender): static {
        if (!in_array($gender, self::GENDERS))
            throw new InvalidArgumentException('Le genre est invalide');

        $this->gender = $gender;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string {
        return $this->password;
    }

    public function setPassword(string $password): static {
        $this->password = $password;

        return $this;
    }

    public function setGestionnaire(): static {
        $roles = $this->getRoles();
        if (!in_array('ROLE_GESTIONNAIRE', $roles)) {
            $roles[] = 'ROLE_GESTIONNAIRE';
            $this->setRoles($roles);
        }
        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static {
        $this->roles = $roles;

        return $this;
    }

    public function removeGestionnaire(): static {
        $roles = array_diff($this->getRoles(), ['ROLE_GESTIONNAIRE']);
        $this->setRoles($roles);
        return $this;
    }

    public function getClient(): ?Client {
        return $this->client;
    }

    public function setClient(Client $client): static {
        // set the owning side of the relation if necessary
        if ($client->getUser() !== $this) {
            $client->setUser($this);
        }

        $this->client = $client;

        return $this;
    }

    public function getIdentity(): string {
        return $this->getFirstName() . " " . $this->getLastName();
    }
}
