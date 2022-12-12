<?php

namespace App\Entity;

use App\Enum\ActionStates;
use App\Enum\ActionTypes;
use App\Repository\ActionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ActionRepository::class)]
#[ORM\Table(name: '`actions`')]
class Action
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'actions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(enumType: ActionTypes::class)]
    private ?ActionTypes $type = null;

    #[ORM\Column(enumType: ActionStates::class)]
    private ?ActionStates $status = ActionStates::TO_BE_RAN;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $runTime = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $data = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getType(): ?ActionTypes
    {
        return $this->type;
    }

    public function setType(ActionTypes $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getStatus(): ?ActionStates
    {
        return $this->status;
    }

    public function setStatus(ActionStates $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getRunTime(): ?\DateTimeInterface
    {
        return $this->runTime;
    }

    public function setRunTime(\DateTimeInterface $runTime): self
    {
        $this->runTime = $runTime;

        return $this;
    }

    public function getData(): string|null
    {
        return $this->data;
    }

    /**
     * @param array|null $data
     * @return Action
     */
    public function setData(?array $data): self
    {
        if(!$data){
            $this->data = null;
            return $this;
        }

        $this->data = json_encode($data) ?: null;
        return $this;
    }
}
