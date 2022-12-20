<?php

namespace App\Controller;

use App\Entity\Action;
use App\Entity\Item;
use App\Entity\User;
use App\Enum\ActionTypes;
use App\Enum\FieldTypes;
use App\Repository\ActionRepository;
use App\Repository\BattleRepository;
use App\Repository\FieldRepository;
use App\Repository\ItemRepository;
use App\Repository\LonerRepository;
use App\Repository\TownRepository;
use App\Repository\UserRepository;
use App\Service\ItemService;
use App\Service\LonerService;
use App\Service\ProductionService;
use App\Service\SquadService;
use App\Service\UserService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MapController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly LonerRepository $lonerRepository,
        private readonly BattleRepository $battleRepository,
        private readonly FieldRepository $fieldRepository,
        private readonly TownRepository $townRepository,
        private readonly ItemRepository $itemRepository,
        private readonly ProductionService $productionService,
        private readonly LonerService $lonerService,
        private readonly SquadService $squadService,
        private readonly ItemService $itemService,
        private readonly UserService $userService,
        private readonly ManagerRegistry $doctrine
    ) {}

    #[Route('/game/map', name: 'game_map')]
    public function index(ActionRepository $actionRepository): Response
    {
        $user = $this->userService->entity($this->getUser());

        $x = $user->getX();
        $y = $user->getY();

        $busy = null;
        $current = null;

        if($user->getBusyTill() != null) {
            $busy = $user->getBusyTill() > new \DateTime() ? $user->getBusyTill()->format('H:i') : null;
        }

        // dočasná hovadinka, která se později zcela nahradí
        $currentAction = $actionRepository->findUserCurrentAction($user);
        if($currentAction !== null){
            $current = [ 'type' => $currentAction->getType(), 'data' => json_decode($currentAction->getData() ?? '[]') ];
        }

        $players = [];
        $allPlayers = $this->userRepository->findAll();
        foreach($allPlayers as $player){
            /* @var $player User */

            $movement = 'none';
            $currentAction = $actionRepository->findUserCurrentAction($player);
            if($currentAction !== null && $currentAction->getType() == ActionTypes::MOVE){
                $target = json_decode($currentAction->getData() ?? '[]', true);
                $options = [
                    [0, -1],
                    [-1, $player->getX() % 2 == 0 ? -1 : 0],
                    [1, $player->getX() % 2 == 0 ? -1 : 0],
                    [-1, $player->getX() % 2 == 0 ? 0 : 1],
                    [1, $player->getX() % 2 == 0 ? 0 : 1],
                    [0, 1],
                ];
                $movements = ['up', 'leftup', 'rightup', 'leftdown', 'rightdown', 'down'];

                foreach($options as $key => $option){
                    if($player->getX() + $option[0] == $target['x'] && $player->getY() + $option[1] == $target['y']){
                        $movement = $movements[$key];
                        break;
                    }
                }
            }

            $players[] = [
                'id' => $player->getId(),
                'username' => $player->getUserIdentifier(),
                'x' => $player->getX(),
                'y' => $player->getY(),
                'size' => count($player->getSoldiers()),
                'faction' => $player->getFaction(),
                'movement' => $movement
            ];
        }

        $loners = [];
        $localLoners = $this->lonerRepository->findBy([ 'x' => $x, 'y' => $y ]);
        foreach($localLoners as $loner){
            $loners[] = [ 'id' => $loner->getId(), 'name' => $loner->getName() ];
        }

        $battles = $this->battleRepository->findAll();
        $towns = $this->townRepository->findAll();
        $items = $this->itemService->getAllItems();

        $fields = [];
        $allFields = $this->fieldRepository->findAll();
        foreach($allFields as $field){
            $fields[] = [ 'x' => $field->getX(), 'y' => $field->getY(), 'type' => $field->getType()->value ];
        }

        $town = $this->townRepository->findOneBy([ 'x' => $x, 'y' => $y ]);
        if($town){
            $this->productionService->produce($town);
        }

        $locationItems = $this->itemRepository->findBy([ 'x' => $x, 'y' => $y ]);

        $location = [
            'town' => $town,
            'items' => $locationItems
        ];

        $speed = $this->squadService->getMovementInterval($user);

        return $this->render('game/map.twig', [
            'x' => $x,
            'y' => $y,
            'location' => $location,
            'players' => $players,
            'loners' => $loners,
            'battles' => $battles,
            'fields' => $fields,
            'towns' => $towns,
            'busy' => $busy,
            'current' => $current,
            'user' => $user,
            'items' => $items,
            'speed' => $speed
        ]);
    }

    // pouze dočasné pro testování
    #[Route('/game/map/move/{x}/{y}', name: 'game_map_move', requirements: ['x' => '\-?[0-9]+', 'y' => '\-?[0-9]+'])]
    public function move(int $x, int $y): Response
    {
        $user = $this->userService->entity($this->getUser());

        //$user->setCoords([ $x, $y ]);

        $options = [
            [0, -1],
            [-1, $user->getX() % 2 == 0 ? -1 : 0],
            [1, $user->getX() % 2 == 0 ? -1 : 0],
            [-1, $user->getX() % 2 == 0 ? 0 : 1],
            [1, $user->getX() % 2 == 0 ? 0 : 1],
            [0, 1],
        ];

        $isValidMove = false;
        foreach($options as $option){
            if($user->getX() + $option[0] == $x && $user->getY() + $option[1] == $y){
                $isValidMove = true;
                break;
            }
        }

        $field = $this->fieldRepository->findOneBy([ 'x' => $x, 'y' => $y ]);
        if($field && in_array($field->getType(), [FieldTypes::WATER, FieldTypes::MOUNTAIN])){
            $isValidMove = false;
        }

        if(!$isValidMove){
            return $this->redirectToRoute('game_map');
        }

        if($user->getBusyTill() > new \DateTime()){
            return $this->redirectToRoute('game_map');
        }

        $secondsToMove = $this->squadService->getMovementInterval($user);

        $user->setBusyTill((new \DateTime())->modify('+' . $secondsToMove . ' seconds'));

        $action = new Action();
        $action->setUser($user);
        $action->setType(ActionTypes::MOVE);
        $action->setRunTime((new \DateTime())->modify('+' . round($secondsToMove / 2) . ' seconds'));
        $action->setData([ 'x' => $x, 'y' => $y ]);

        $this->doctrine->getManager()->persist($action);
        $this->doctrine->getManager()->flush();

        return $this->redirectToRoute('game_map');
    }

    #[Route('/game/recruit/{id}', name: 'game_recruit', requirements: ['id' => '\d+'])]
    public function recruit(int $id): Response
    {
        $user = $this->userService->entity($this->getUser());

        $loner = $this->lonerRepository->find($id);
        if(!$loner){
            throw new \RuntimeException("No loner with that ID!");
        }

        if($loner->getX() != $user->getX() || $loner->getY() != $user->getY()){
            throw new \RuntimeException("Thank You Mario, But Our Princess is in Another Castle!");
        }

        $this->lonerService->assignLonerToUser($loner, $user);

        return $this->redirectToRoute('game_map');
    }

    #[Route('/game/map/capture', name: 'game_map_capture')]
    public function capture(): Response
    {
        $user = $this->userService->entity($this->getUser());

        if(!$user->getFaction()){
            return $this->redirectToRoute('game_map');
        }

        $town = $this->townRepository->findOneBy([ 'x' => $user->getX(), 'y' => $user->getY() ]);
        if(!$town || $town->getOwner() === $user->getFaction()){
            return $this->redirectToRoute('game_map');
        }

        $enemySize = 0;
        $players = $this->userRepository->findBy([ 'x' => $user->getX(), 'y' => $user->getY() ]);
        foreach($players as $player){
            // TODO: později nahradit diplomacií
            if($player->getFaction() !== $user->getFaction()){
                $enemySize += count($player->getSoldiers());
            }
        }

        if($enemySize > 0){
            return $this->redirectToRoute('game_map');
        }

        $town->setOwner($user->getFaction());

        $user->setBusyTill(new \DateTime('10 minute'));

        $this->doctrine->getManager()->flush();

        return $this->redirectToRoute('game_map');
    }

    #[Route('/game/map/produce/{id}', name: 'game_map_produce', requirements: ['id' => '\d+'])]
    public function produce(int $id): Response
    {
        $user = $this->userService->entity($this->getUser());

        if(!$user->getFaction()){
            return $this->redirectToRoute('game_map');
        }

        $town = $this->townRepository->findOneBy([ 'x' => $user->getX(), 'y' => $user->getY() ]);
        if(!$town || $town->getOwner() !== $user->getFaction()){
            return $this->redirectToRoute('game_map');
        }

        $item = $this->itemService->getItem($id);
        if(!$item || $item->getProductionTime() == 0){
            return $this->redirectToRoute('game_map');
        }

        $town->setProduction($id);
        $town->setLastProduced(new \DateTime());

        $this->doctrine->getManager()->flush();

        return $this->redirectToRoute('game_map');
    }

    #[Route('/game/map/pickup/{id}/{amount}', name: 'game_map_pickup', requirements: ['id' => '\d+', 'amount' => '\d+'])]
    public function pickup(int $id, int $amount): Response
    {
        $user = $this->userService->entity($this->getUser());

        $item = $this->itemRepository->find($id);
        if(!$item || $item->getX() !== $user->getX() || $item->getY() !== $user->getY()){
            return $this->redirectToRoute('game_map');
        }

        $itemData = $this->itemService->getItem($item->getItemId());
        if(!$itemData){
            return $this->redirectToRoute('game_map');
        }

        if($item->getQuantity() < $amount){
            return $this->redirectToRoute('game_map');
        }

        if($item->getQuantity() == $amount){
            $item->setUser($user)->setX(null)->setY(null);

            if($itemData->isStackable()){
                $existing = $this->itemRepository->findOneBy([ 'user' => $user, 'itemId' => $item->getItemId() ]);
                if($existing){
                    $existing->setQuantity($existing->getQuantity() + $amount);
                    $this->doctrine->getManager()->remove($item);
                }
            }
        }elseif($itemData->isStackable()){
            $item->setQuantity($item->getQuantity() - $amount);

            $existing = $this->itemRepository->findOneBy([ 'user' => $user, 'itemId' => $item->getItemId() ]);
            if($existing){
                $existing->setQuantity($existing->getQuantity() + $amount);
            }else{
                $newItem = new Item();
                $newItem->setItemId($item->getItemId())->setUser($user);
                $this->doctrine->getManager()->persist($newItem);
            }
        }

        $this->doctrine->getManager()->flush();

        return $this->redirectToRoute('game_map');
    }

    #[Route('/game/map/world', name: 'game_map_world')]
    public function world(): Response
    {
        $fields = [];
        $allFields = $this->fieldRepository->findAll();
        foreach($allFields as $field){
            $fields[] = [ 'x' => $field->getX(), 'y' => $field->getY(), 'type' => $field->getType()->value ];
        }

        $towns = $this->townRepository->findAll();
        return $this->render('game/worldmap.twig', [ 'players' => [], 'battles' => [], 'fields' => $fields, 'towns' => $towns ]);
    }
}
