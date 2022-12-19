<?php

namespace App\Entity;

use App\Repository\TownRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TownRepository::class)]
#[ORM\Table(name: '`towns`')]
class Town
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column]
    private ?int $x = null;

    #[ORM\Column]
    private ?int $y = null;

    #[ORM\ManyToOne(inversedBy: 'towns')]
    private ?Faction $owner = null;

    #[ORM\Column(nullable: true)]
    private ?int $production = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $lastProduced = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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

    public function getOwner(): ?Faction
    {
        return $this->owner;
    }

    public function setOwner(?Faction $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getProduction(): ?int
    {
        return $this->production;
    }

    public function setProduction(?int $production): self
    {
        $this->production = $production;

        return $this;
    }

    public function getLastProduced(): ?\DateTimeInterface
    {
        return $this->lastProduced;
    }

    public function setLastProduced(?\DateTimeInterface $lastProduced): self
    {
        $this->lastProduced = $lastProduced;

        return $this;
    }
}
