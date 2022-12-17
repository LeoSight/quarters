<?php

namespace App\Service;

use App\Entity\Item;
use App\Model\Ammo;
use App\Model\GenericItem;
use App\Model\ItemInterface;
use App\Model\Weapon;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;

class ItemService {

    private ObjectManager $manager;

    /**
     * @var array<int, ItemInterface>
     */
    private array $dictionary = [];

    public function __construct(
        private readonly ManagerRegistry $doctrine
    )
    {
        $this->manager = $this->doctrine->getManager();
        $this->fillItemDictionary();
    }

    public function getItem(int $id): ItemInterface
    {
        return $this->dictionary[$id];
    }

    public function deplete(Item $item, ?int $quantity = 1): bool
    {
        if($item->getQuantity() < $quantity){
            return false;
        }

        $item->setQuantity($item->getQuantity() - $quantity);

        if($item->getQuantity() < 1){
            $this->manager->remove($item);
        }

        $this->manager->flush();

        return true;
    }

    public function fillItemDictionary(): void
    {
        $item = new GenericItem();
        $item->setName("Zdravotnický materiál");
        $item->setWeight(0.2);
        $item->setStackable(true);
        $this->dictionary[1] = $item;

        // MUNICE

        $item = new Ammo();
        $item->setName("Munice 7.62×39mm");
        $item->setWeight(0.024);
        $item->setStackable(true);
        $this->dictionary[2] = $item;

        $item = new Ammo();
        $item->setName("Munice 7.62×51mm");
        $item->setWeight(0.034);
        $item->setStackable(true);
        $this->dictionary[3] = $item;

        $item = new Ammo();
        $item->setName("Munice 5.56×45mm");
        $item->setWeight(0.016);
        $item->setStackable(true);
        $this->dictionary[4] = $item;

        $item = new Ammo();
        $item->setName("Munice 12 Gauge");
        $item->setWeight(0.045);
        $item->setStackable(true);
        $this->dictionary[5] = $item;

        $item = new Ammo();
        $item->setName("Munice .22 Long Rifle");
        $item->setWeight(0.01);
        $item->setStackable(true);
        $this->dictionary[6] = $item;

        $item = new Ammo();
        $item->setName("Munice 9×19mm");
        $item->setWeight(0.014);
        $item->setStackable(true);
        $this->dictionary[7] = $item;

        $item = new Ammo();
        $item->setName("Munice .45 ACP");
        $item->setWeight(0.027);
        $item->setStackable(true);
        $this->dictionary[8] = $item;

        // ZBRANĚ

        $item = new Weapon();
        $item->setName("AK-47");
        $item->setWeight(3.5);
        $item->setAccuracy(0.35);
        $item->setRateOfFire(500);
        $item->setMuzzleVelocity(715);
        $item->setAmmoType($this->dictionary[2]); // 7.62x39
        $this->dictionary[9] = $item;

        $item = new Weapon();
        $item->setName("H&K G3");
        $item->setWeight(4.5);
        $item->setAccuracy(0.4);
        $item->setRateOfFire(600);
        $item->setMuzzleVelocity(800);
        $item->setAmmoType($this->dictionary[3]); // 7.62x51
        $this->dictionary[10] = $item;
    }

}