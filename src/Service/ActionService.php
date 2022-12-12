<?php

namespace App\Service;

use App\Entity\Action;
use App\Enum\ActionStates;
use App\Enum\ActionTypes;
use App\Model\MoveAction;
use App\Repository\ActionRepository;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ActionService {

    private Serializer $serializer;

    public function __construct(
        private readonly ActionRepository $actionRepository,
        private readonly LonerService $lonerService
    )
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $this->serializer = new Serializer($normalizers, $encoders);
    }

    public function processActions(): void
    {
        $actions = $this->actionRepository->findActionsToBeRan();
        foreach($actions as $action){
            $this->runAction($action);
        }
    }

    public function runAction(Action $action): void
    {
        // TODO: až budeme v budoucnu přidávat více časovaných akcí, bude to tu nutné překopat
        if($action->getType() != ActionTypes::MOVE){
            throw new \RuntimeException("It's middleware time! 💀");
        }

        if(!$action->getUser()){
            throw new \RuntimeException("Move action needs user!");
        }

        /* @var MoveAction $data */
        $data = $this->serializer->deserialize($action->getData(), MoveAction::class, 'json');
        if(!$data instanceof MoveAction){
            throw new \RuntimeException("Data cannot be serialized!");
        }

        $this->lonerService->findLonerByChance($data->getX(), $data->getY());

        $action->getUser()->setCoords([ $data->getX(), $data->getY() ]);
        $action->setStatus(ActionStates::DONE);
    }

}