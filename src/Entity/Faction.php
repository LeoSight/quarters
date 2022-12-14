<?php

namespace App\Entity;

use App\Repository\FactionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FactionRepository::class)]
#[ORM\Table(name: '`factions`')]
class Faction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\ManyToOne]
    private ?User $leader = null;

    #[ORM\Column(length: 6)]
    private ?string $color = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $flag = null;

    /**
     * @var ArrayCollection<int, User>
     */
    #[ORM\OneToMany(mappedBy: 'faction', targetEntity: User::class)]
    private Collection $users;

    /**
     * @var ArrayCollection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'applications')]
    #[ORM\JoinTable(name: "factions_applicants")]
    private Collection $applicants;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->applicants = new ArrayCollection();
    }

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

    public function getLeader(): ?User
    {
        return $this->leader;
    }

    public function setLeader(?User $leader): self
    {
        $this->leader = $leader;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getFlag(): ?string
    {
        return $this->flag;
    }

    public function setFlag(?string $flag): self
    {
        $this->flag = $flag;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setFaction($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getFaction() === $this) {
                $user->setFaction(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getApplicants(): Collection
    {
        return $this->applicants;
    }

    public function addApplicant(User $applicant): self
    {
        if (!$this->applicants->contains($applicant)) {
            $this->applicants->add($applicant);
        }

        return $this;
    }

    public function removeApplicant(User $applicant): self
    {
        $this->applicants->removeElement($applicant);

        return $this;
    }
}
