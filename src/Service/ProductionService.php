<?php

namespace App\Service;

use App\Entity\Item;
use App\Entity\Town;
use App\Repository\ItemRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;

class ProductionService {

    private ObjectManager $manager;

    public function __construct(
        private readonly ManagerRegistry $doctrine,
        private readonly ItemRepository $itemRepository,
        private readonly ItemService $itemService
    ) {
        $this->manager = $this->doctrine->getManager();
    }

    public function produce(Town $town): void
    {
        if(!$town->getProduction()){
            return;
        }

        $item = $this->itemService->getItem($town->getProduction());
        if(!$item || $item->getProductionTime() == 0){
            return;
        }

        $lastProduced = $town->getLastProduced() ?? new \DateTime('@0');
        $minutesSinceLastProduced = (new \DateTime())->setTimeStamp(0)->add($lastProduced->diff(new \DateTime()))->getTimeStamp() / 60;
        if($minutesSinceLastProduced < $item->getProductionTime()){
            return;
        }

        $howMany = (int)floor($minutesSinceLastProduced / $item->getProductionTime());
        $minutesLeft = floor($minutesSinceLastProduced - $howMany * $item->getProductionTime());
        $town->setLastProduced((new \DateTime())->modify('-' . $minutesLeft . ' minutes'));

        if($item->isStackable()){
            $existing = $this->itemRepository->findOneBy([ 'x' => $town->getX(), 'y' => $town->getY(), 'itemId' => $item->getId() ]);
            if($existing){
                $existing->setQuantity($existing->getQuantity() + $howMany);
            }else{
                $itemInstance = new Item();
                $itemInstance->setItemId($item->getId())
                    ->setX($town->getX())
                    ->setY($town->getY())
                    ->setQuantity($howMany);
                $this->manager->persist($itemInstance);
            }
        }else{
            for($i = 0; $i <= $howMany; $i++) {
                $itemInstance = new Item();
                $itemInstance->setItemId($item->getId())
                    ->setX($town->getX())
                    ->setY($town->getY());
                $this->manager->persist($itemInstance);
            }
        }

        $this->manager->flush();
    }

}