<?php

namespace App\Entity;

use App\Model\ItemInterface;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
    private int $x = 0;

    #[ORM\Column]
    private int $y = 0;

    /**
     * @var ArrayCollection<int, Soldier>
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Soldier::class)]
    private Collection $soldiers;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $busyTill = null;

    /**
     * @var ArrayCollection<int, Action>
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Action::class)]
    private Collection $actions;

    /**
     * @var ArrayCollection<int, Faction>
     */
    #[ORM\ManyToMany(targetEntity: Faction::class, mappedBy: 'applicants')]
    #[ORM\JoinTable(name: "factions_applicants")]
    private Collection $applications;

    /**
     * @var ArrayCollection<int, Item>
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Item::class)]
    private Collection $items;

    #[ORM\Column(options: ["default" => 0])]
    private int $kills = 0;

    #[ORM\Column(options: ["default" => 0])]
    private int $deaths = 0;

    public function __construct()
    {
        $this->soldiers = new ArrayCollection();
        $this->actions = new ArrayCollection();
        $this->applications = new ArrayCollection();
        $this->items = new ArrayCollection();
    }

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
    public function eraseCredentials(): void
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

    /**
     * @return array<int, int>
     */
    public function getCoords(): array
    {
        return [$this->x, $this->y];
    }

    /**
     * @param array<int, int> $coords
     */
    public function setCoords(array $coords): self
    {
        $this->x = $coords[0];
        $this->y = $coords[1];

        return $this;
    }

    /**
     * @return Collection<int, Soldier>
     */
    public function getSoldiers(): Collection
    {
        return $this->soldiers;
    }

    public function addSoldier(Soldier $soldier): self
    {
        if (!$this->soldiers->contains($soldier)) {
            $this->soldiers->add($soldier);
            $soldier->setUser($this);
        }

        return $this;
    }

    public function removeSoldier(Soldier $soldier): self
    {
        if ($this->soldiers->removeElement($soldier)) {
            // set the owning side to null (unless already changed)
            if ($soldier->getUser() === $this) {
                $soldier->setUser(null);
            }
        }

        return $this;
    }

    public function getBusyTill(): ?\DateTimeInterface
    {
        return $this->busyTill;
    }

    public function setBusyTill(?\DateTimeInterface $busyTill): self
    {
        $this->busyTill = $busyTill;

        return $this;
    }

    /**
     * @return Collection<int, Action>
     */
    public function getActions(): Collection
    {
        return $this->actions;
    }

    public function addAction(Action $action): self
    {
        if (!$this->actions->contains($action)) {
            $this->actions->add($action);
            $action->setUser($this);
        }

        return $this;
    }

    public function removeAction(Action $action): self
    {
        if ($this->actions->removeElement($action)) {
            // set the owning side to null (unless already changed)
            if ($action->getUser() === $this) {
                $action->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Faction>
     */
    public function getApplications(): Collection
    {
        return $this->applications;
    }

    public function addApplication(Faction $application): self
    {
        if (!$this->applications->contains($application)) {
            $this->applications->add($application);
            $application->addApplicant($this);
        }

        return $this;
    }

    public function removeApplication(Faction $application): self
    {
        if ($this->applications->removeElement($application)) {
            $application->removeApplicant($this);
        }

        return $this;
    }

    public function removeAllApplications(): self
    {
        $this->applications->forAll(function(int $key, Faction $faction): bool {
            $this->removeApplication($faction);
            $faction->removeApplicant($this);
            return true;
        });

        return $this;
    }

    /**
     * @return Collection<int, Item>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(Item $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setUser($this);
        }

        return $this;
    }

    public function removeItem(Item $item): self
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getUser() === $this) {
                $item->setUser(null);
            }
        }

        return $this;
    }

    public function getKills(): ?int
    {
        return $this->kills;
    }

    public function setKills(int $kills): self
    {
        $this->kills = $kills;

        return $this;
    }

    public function getDeaths(): ?int
    {
        return $this->deaths;
    }

    public function setDeaths(int $deaths): self
    {
        $this->deaths = $deaths;

        return $this;
    }
}
