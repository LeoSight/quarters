<?php

namespace App\Model;

interface ItemInterface {

    public function getId(): int;
    public function getName(): string;
    public function getWeight(): float;
    public function isStackable(): bool;
    public function getProductionTime(): float;
    public function use(): void;

}