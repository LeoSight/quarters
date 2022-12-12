<?php

namespace App\Model;

class LoginData {

    private bool $logged = false;
    private ?int $id = null;
    private ?string $username = null;

    public function getLogged(): bool
    {
        return $this->logged;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setLogged(bool $logged): self
    {
        $this->logged = $logged;

        return $this;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;

        return $this;
    }

}