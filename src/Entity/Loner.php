<?php

namespace App\Entity;

use App\Enum\SoldierRoles;
use App\Repository\LonerRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LonerRepository::class)]
#[ORM\Table(name: '`loners`')]
class Loner
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $x = null;

    #[ORM\Column]
    private ?int $y = null;

    #[ORM\Column(enumType: SoldierRoles::class)]
    private ?SoldierRoles $role = SoldierRoles::CITIZEN;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $health = 100;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $experience = 0;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $weapon = 0;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $injuries = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getX(): ?int
    {
        return $this->x;
    }

    public function setX(?int $x): self
    {
        $this->x = $x;

        return $this;
    }

    public function getY(): ?int
    {
        return $this->y;
    }

    public function setY(?int $y): self
    {
        $this->y = $y;

        return $this;
    }

    public function getRole(): ?SoldierRoles
    {
        return $this->role;
    }

    public function setRole(SoldierRoles $role): self
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

    /**
     * @return string[]|null Returns an array of strings or null
     */
    public function getInjuries(): ?array
    {
        $injuries = json_decode($this->injuries ?? '[]', true);
        if(!is_array($injuries)){
            return null;
        }

        return $injuries;
    }

    /**
     * @param array<int, string> $injuries
     */
    public function setInjuries(?array $injuries): self
    {
        if(!$injuries){
            $this->injuries = null;
            return $this;
        }

        $this->injuries = json_encode($injuries) ?: null;
        return $this;
    }
}
