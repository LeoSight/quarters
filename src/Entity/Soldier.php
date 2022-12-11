<?php

namespace App\Entity;

use App\Repository\SoldierRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SoldierRepository::class)]
#[ORM\Table(name: '`soldiers`')]
class Soldier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'soldiers')]
    private ?User $user = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $role = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $health = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $experience = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $weapon = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private array $injuries = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getRole(): ?int
    {
        return $this->role;
    }

    public function setRole(int $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getHealth(): ?int
    {
        return $this->health;
    }

    public function setHealth(int $health): self
    {
        $this->health = $health;

        return $this;
    }

    public function getExperience(): ?int
    {
        return $this->experience;
    }

    public function setExperience(int $experience): self
    {
        $this->experience = $experience;

        return $this;
    }

    public function getWeapon(): ?int
    {
        return $this->weapon;
    }

    public function setWeapon(int $weapon): self
    {
        $this->weapon = $weapon;

        return $this;
    }

    public function getInjuries(): array
    {
        return $this->injuries;
    }

    public function setInjuries(?array $injuries): self
    {
        $this->injuries = $injuries;

        return $this;
    }
}
