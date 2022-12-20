<?php

namespace App\Service;

use App\Entity\Soldier;
use App\Entity\User;

class SquadService {

    public function getMaxCarryWeight(User $user): int
    {
        return count($user->getSoldiers()) * 30;
    }

    public function getMovementInterval(User $user): int
    {
        $soldiersTotal = count($user->getSoldiers());
        $severelyInjured = count($user->getSoldiers()->filter(function(Soldier $soldier){
            return $soldier->getHealth() < 30;
        }));

        return 90 + $soldiersTotal * 3 + $severelyInjured * 20;
    }

    public function getFirePower(User $user): int
    {
        $firePower = count($user->getSoldiers()->filter(function(Soldier $soldier){
            return $soldier->getHealth() >= 30;
        }));

        return max(1, $firePower);
    }

}