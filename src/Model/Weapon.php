<?php

namespace App\Model;

class Weapon extends GenericItem implements ItemInterface {

    private float $accuracy;
    private int $rateOfFire;
    private int $muzzleVelocity;

    /**
     * @return float
     */
    public function getAccuracy(): float
    {
        return $this->accuracy;
    }

    /**
     * @return int
     */
    public function getRateOfFire(): int
    {
        return $this->rateOfFire;
    }

    /**
     * @return int
     */
    public function getMuzzleVelocity(): int
    {
        return $this->muzzleVelocity;
    }

    /**
     * @param float $accuracy
     * @return Weapon
     */
    public function setAccuracy(float $accuracy): Weapon
    {
        $this->accuracy = $accuracy;
        return $this;
    }

    /**
     * @param int $rateOfFire
     * @return Weapon
     */
    public function setRateOfFire(int $rateOfFire): Weapon
    {
        $this->rateOfFire = $rateOfFire;
        return $this;
    }

    /**
     * @param int $muzzleVelocity
     * @return Weapon
     */
    public function setMuzzleVelocity(int $muzzleVelocity): Weapon
    {
        $this->muzzleVelocity = $muzzleVelocity;
        return $this;
    }

}