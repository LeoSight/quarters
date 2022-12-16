<?php

namespace App\Model;

class GenericItem implements ItemInterface {

    private string $name;
    private float $weight;
    private bool $stackable = false;

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

    public function use(): void
    {
        // bez funkce
    }
}