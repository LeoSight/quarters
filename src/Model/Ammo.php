<?php

namespace App\Model;

class Ammo extends GenericItem implements ItemInterface {

    public function isStackable(): bool
    {
        return true;
    }

}