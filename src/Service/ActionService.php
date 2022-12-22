<?php

namespace App\Service;

use App\Entity\Action;
use App\Entity\Item;
use App\Entity\Notification;
use App\Entity\Soldier;
use App\Enum\ActionStates;
use App\Enum\ActionTypes;
use App\Enum\NotificationTypes;
use App\Enum\SoldierRoles;
use App\Model\MoveAction;
use App\Repository\ActionRepository;
use App\Repository\BattleRepository;
use App\Repository\ItemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ActionService {

    private Serializer $serializer;
    private ObjectManager $manager;

    public function __construct(
        private readonly ActionRepository $actionRepository,
        private readonly ItemRepository $itemRepository,
        private readonly BattleRepository $battleRepository,
        private readonly ItemService $itemService,
        private readonly LonerService $lonerService,
        private readonly BattleService $battleService,
        private readonly ManagerRegistry $doctrine
    )
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $this->serializer = new Serializer($normalizers, $encoders);
        $this->manager = $this->doctrine->getManager();
    }

    public function processActions(): void
    {
        $actions = $this->actionRepository->findActionsToBeRan();
        foreach($actions as $action){
            $this->runAction($action);
        }

        // smazání starých akcí
        $oldActions = $this->actionRepository->findOldActions();
        foreach($oldActions as $action){
            $this->manager->remove($action);
        }

        $this->manager->flush();
    }

    public function runAction(Action $action): void
    {
        // TODO: až budeme v budoucnu přidávat více časovaných akcí, bude to tu nutné překopat
        if($action->getType() == ActionTypes::MOVE){
            $this->runActionMove($action);
        }elseif($action->getType() == ActionTypes::REST){
            $this->runActionRest($action);
        }else{
            throw new \RuntimeException("It's middleware time! 💀");
        }
    }

    public function runActionMove(Action $action): void
    {
        $user = $action->getUser();
        if(!$user){
            throw new \RuntimeException("Move action needs user!");
        }

        /* @var MoveAction $data */
        $data = $this->serializer->deserialize($action->getData(), MoveAction::class, 'json');
        if(!$data instanceof MoveAction){
            throw new \RuntimeException("Data cannot be serialized!");
        }

        $this->lonerService->findLonerByChance($data->getX(), $data->getY());

        $soldiers = $user->getSoldiers();

        // při útěku z bitvy
        $battle = $this->battleRepository->findBy(['x' => $user->getX(), 'y' => $user->getY()]);
        if($battle){
            foreach ($soldiers as $soldier) {
                $soldier->setMorale(max(0, $soldier->getMorale() - rand(1, 4)));
            }
        }

        if(count($soldiers) > 16) {
            foreach ($soldiers as $soldier) {
                if ($soldier->getMorale() < 20 && $soldier->getHealth() > 50 && rand(1, 5) == 1) {
                    $notification = new Notification();
                    $notification->setType(NotificationTypes::WARNING);
                    $notification->setMessage('Máme tu dezertéra! ' . $soldier->getName() . ' vzal roha!');
                    $user->addNotification($notification);
                    $this->manager->persist($notification);
                    $this->manager->remove($soldier);
                }
            }
        }

        $user->setCoords([ $data->getX(), $data->getY() ]);
        $action->setStatus(ActionStates::DONE);

        $this->manager->flush();

        $this->battleService->startBattles();
    }

    public function runActionRest(Action $action): void
    {
        $user = $action->getUser();
        if(!$user){
            throw new \RuntimeException("Rest action needs user!");
        }

        $soldiers = $user->getSoldiers();

        if(count($soldiers) > 0) {

            // LÉČENÍ

            $injured = $soldiers->filter(function (Soldier $soldier) {
                return $soldier->getHealth() < 100 || count($soldier->getInjuries() ?? []) > 0;
            });

            $criteria = Criteria::create()->orderBy(["health" => Criteria::ASC]);
            $injured = (new ArrayCollection($injured->toArray()))->matching($criteria);

            $medics = count($soldiers->filter(function ($soldier) {
                return $soldier->getRole() == SoldierRoles::MEDIC;
            }));
            $healPower = $medics * 30;

            $medKits = $this->itemRepository->findOneBy(['user' => $user, 'itemId' => 1]);

            foreach ($injured as $soldier) {
                $heal = rand(1, 3);
                if ($healPower > 0) {
                    $heal = min($healPower, 100 - $soldier->getHealth());
                    if ($heal > 20) {
                        if (!$medKits || !$this->itemService->deplete($medKits)) {
                            $heal = rand(1, 5);
                        }
                    }

                    $healPower -= $heal;
                }

                $soldier->setHealth(min(100, $soldier->getHealth() + $heal));
                $soldier->setInjuries(null);
            }

            // MORÁLKA

            $totalMorale = 0;
            foreach ($soldiers as $soldier) {
                $totalMorale += $soldier->getMorale();
            }

            $avgMorale = round($totalMorale / count($soldiers));
            $moraleBoost = (int)ceil(max(0, (16 - count($soldiers))) / 3);
            foreach ($soldiers as $soldier) {
                $morale = $soldier->getMorale();
                $soldier->setMorale($morale + ($avgMorale <=> $morale) * (int)ceil(0.1 * abs($avgMorale - $morale)) + $moraleBoost);
            }

        }

        $action->setStatus(ActionStates::DONE);

        $this->manager->flush();
    }

}