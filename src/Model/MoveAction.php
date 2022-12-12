<?php

namespace App\Model;

class MoveAction {

    private int $x;
    private int $y;

    public function getX(): int
    {
        return $this->x;
    }

    public function getY(): int
    {
        return $this->y;
    }

    public function setX(int $x): self
    {
        $this->x = $x;

        return $this;
    }

    public function setY(int $y): self
    {
        $this->y = $y;

        return $this;
    }

}