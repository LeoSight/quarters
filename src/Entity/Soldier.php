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
