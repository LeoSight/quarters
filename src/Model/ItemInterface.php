<?php

namespace App\Model;

interface ItemInterface {

    public function getName(): string;
    public function getWeight(): float;
    public function use(): void;

}