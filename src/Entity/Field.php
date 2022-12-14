<?php

namespace App\Entity;

use App\Enum\FieldTypes;
use App\Repository\FieldRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FieldRepository::class)]
#[ORM\Table(name: '`fields`')]
class Field
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $x = null;

    #[ORM\Column]
    private ?int $y = null;

    #[ORM\Column(enumType: FieldTypes::class)]
    private FieldTypes $type = FieldTypes::EMPTY;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getType(): FieldTypes
    {
        return $this->type;
    }

    public function setType(FieldTypes $type): self
    {
        $this->type = $type;

        return $this;
    }
}
