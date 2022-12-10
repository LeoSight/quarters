<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`users`')]
class User implements UserInterface
{
    #[ORM\Id]
    #[ORM\Column(length: 255, unique: true)]
    private ?int $id = null;

    #[ORM\Column(length: 190, unique: true)]
    private ?string $username = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $lastSeen = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    private ?Faction $faction = null;

    #[ORM\Column]
    private ?int $x = 0;

    #[ORM\Column]
    private ?int $y = 0;

    /*
    #[ORM\Column(type: 'json')]
    private array $roles = [];
    */

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        /*
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
        */
        return ['ROLE_USER'];
    }

    /*
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }
    */

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getLastSeen(): ?\DateTimeInterface
    {
        return $this->lastSeen;
    }

    public function setLastSeen(?\DateTimeInterface $lastSeen): self
    {
        $this->lastSeen = $lastSeen;

        return $this;
    }

    public function getFaction(): ?Faction
    {
        return $this->faction;
    }

    public function setFaction(?Faction $faction): self
    {
        $this->faction = $faction;

        return $this;
    }

    public function getX(): ?int
    {
        return $this->x;
    }

    public function setX(int $x): self
    {
        $this->x = $x;

        return $this;
    }

    public function getY(): ?int
    {
        return $this->y;
    }

    public function setY(int $y): self
    {
        $this->y = $y;

        return $this;
    }

    public function getCoords(): ?array
    {
        return [$this->x, $this->y];
    }

    public function setCoords(array $coords): self
    {
        $this->x = $coords[0];
        $this->y = $coords[1];

        return $this;
    }
}
