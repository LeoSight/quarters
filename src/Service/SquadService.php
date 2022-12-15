<?php

namespace App\Service;

use App\Entity\User;

class SquadService {

    public function getMaxCarryWeight(User $user): int
    {
        return count($user->getSoldiers()) * 30;
    }

}