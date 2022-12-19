<?php

namespace App\Model;

class GenericItem implements ItemInterface {

    private int $id;
    private string $name;
    private float $weight;
    private bool $stackable = false;
    private float $productionTime = 0;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getWeight(): float
    {
        return $this->weight;
    }

    public function setWeight(float $weight): self
    {
        $this->weight = $weight;
        return $this;
    }

    public function isStackable(): bool
    {
        return $this->stackable;
    }

    public function setStackable(bool $stackable): self
    {
        $this->stackable = $stackable;
        return $this;
    }

    public function getProductionTime(): float
    {
        return $this->productionTime;
    }

    public function setProductionTime(float $productionTime): self
    {
        $this->productionTime = $productionTime;
        return $this;
    }

    public function use(): void
    {
        // bez funkce
    }
}