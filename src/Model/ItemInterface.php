<?php

namespace App\Model;

interface ItemInterface {

    public function getName(): string;
    public function getWeight(): float;
    public function isStackable(): bool;
    public function use(): void;

}