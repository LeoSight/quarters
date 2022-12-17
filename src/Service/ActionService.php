<?php

namespace App\Service;

use App\Entity\Action;
use App\Enum\ActionStates;
use App\Enum\ActionTypes;
use App\Enum\SoldierRoles;
use App\Model\MoveAction;
use App\Repository\ActionRepository;
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
        $medics = count($soldiers->filter(function($soldier){ return $soldier->getRole() == SoldierRoles::MEDIC; }));

        foreach($soldiers as $soldier){
            if($soldier->getHealth() < 100 || count($soldier->getInjuries() ?? []) > 0){
                $soldier->setHealth(min(100, $soldier->getHealth() + rand(1,3) * (1 + $medics)));
                $soldier->setInjuries(null);
            }
        }

        $action->setStatus(ActionStates::DONE);

        $this->manager->flush();
    }

}