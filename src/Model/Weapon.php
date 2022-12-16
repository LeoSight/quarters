<?php

namespace App\Model;

class Weapon extends GenericItem implements ItemInterface {

    private float $accuracy;
    private int $rateOfFire;
    private int $muzzleVelocity;
    private Ammo $ammoType;

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
    public function setAccuracy(float $accuracy): self
    {
        $this->accuracy = $accuracy;
        return $this;
    }

    /**
     * @param int $rateOfFire
     * @return Weapon
     */
    public function setRateOfFire(int $rateOfFire): self
    {
        $this->rateOfFire = $rateOfFire;
        return $this;
    }

    /**
     * @param int $muzzleVelocity
     * @return Weapon
     */
    public function setMuzzleVelocity(int $muzzleVelocity): self
    {
        $this->muzzleVelocity = $muzzleVelocity;
        return $this;
    }

    /**
     * @return Ammo
     */
    public function getAmmoType(): Ammo
    {
        return $this->ammoType;
    }

    /**
     * @param Ammo $ammoType
     * @return Weapon
     */
    public function setAmmoType(Ammo $ammoType): self
    {
        $this->ammoType = $ammoType;
        return $this;
    }

}