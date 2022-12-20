<?php

namespace App\Controller;

use App\Entity\Action;
use App\Entity\Item;
use App\Enum\ActionTypes;
use App\Repository\ActionRepository;
use App\Repository\BattleRepository;
use App\Repository\ItemRepository;
use App\Repository\SoldierRepository;
use App\Service\ItemService;
use App\Service\LonerService;
use App\Service\SquadService;
use App\Service\UserService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SquadController extends AbstractController
{
    public function __construct(
        private readonly SoldierRepository $soldierRepository,
        private readonly ActionRepository $actionRepository,
        private readonly BattleRepository $battleRepository,
        private readonly ItemRepository $itemRepository,
        private readonly ItemService $itemService,
        private readonly LonerService $lonerService,
        private readonly SquadService $squadService,
        private readonly UserService $userService,
        private readonly ManagerRegistry $doctrine
    ) {}

    #[Route('/game/squad', name: 'game_squad')]
    public function index(): Response
    {
        $user = $this->userService->entity($this->getUser());

        $soldiers = $this->soldierRepository->findBy([ 'user' => $this->getUser() ]);
        $inventory = $this->itemRepository->findBy([ 'user' => $this->getUser() ]);
        $battle = $this->battleRepository->findOneBy([ 'x' => $user->getX(), 'y' => $user->getY() ]);
        $items = $this->itemService->getAllItems();

        $busy = null;
        $current = null;

        if($user->getBusyTill() != null) {
            $busy = $user->getBusyTill() > new \DateTime() ? $user->getBusyTill()->format('H:i') : null;
        }

        $currentAction = $this->actionRepository->findUserCurrentAction($user);
        if($currentAction !== null){
            $current = [ 'type' => $currentAction->getType(), 'data' => json_decode($currentAction->getData() ?? '[]') ];
        }

        $speed = $this->squadService->getMovementInterval($user);

        return $this->render('game/squad.twig', [
            'soldiers' => $soldiers,
            'inventory' => $inventory,
            'items' => $items,
            'battle' => $battle,
            'busy' => $busy,
            'current' => $current,
            'speed' => $speed
        ]);
    }

    #[Route('/game/squad/kick/{id}', name: 'game_squad_kick', requirements: ['id' => '\d+'])]
    public function kick(int $id): Response
    {
        $user = $this->userService->entity($this->getUser());

        $soldier = $this->soldierRepository->find($id);
        if (!$soldier || $soldier->getUser() !== $user) {
            return $this->redirectToRoute('game_squad');
        }

        $this->lonerService->freeSoldierFromUser($soldier, $user);

        return $this->redirectToRoute('game_squad');
    }

    #[Route('/game/squad/rest', name: 'game_squad_rest')]
    public function rest(): Response
    {
        $user = $this->userService->entity($this->getUser());

        $battle = $this->battleRepository->findOneBy([ 'x' => $user->getX(), 'y' => $user->getY() ]);
        if($battle){
            return $this->redirectToRoute('game_squad');
        }

        if($user->getBusyTill() > new \DateTime()){
            return $this->redirectToRoute('game_squad');
        }

        $user->setBusyTill(new \DateTime('5 minute'));

        $action = new Action();
        $action->setUser($user);
        $action->setType(ActionTypes::REST);
        $action->setRunTime(new \DateTime('4 minute'));

        $this->doctrine->getManager()->persist($action);
        $this->doctrine->getManager()->flush();

        return $this->redirectToRoute('game_squad');
    }

    #[Route('/game/squad/drop/{id}/{amount}', name: 'game_squad_drop', requirements: ['id' => '\d+', 'amount' => '\d+'])]
    public function drop(int $id, int $amount): Response
    {
        $user = $this->userService->entity($this->getUser());

        $item = $this->itemRepository->find($id);
        if(!$item || $item->getUser() !== $user){
            return $this->redirectToRoute('game_squad');
        }

        $itemData = $this->itemService->getItem($item->getItemId());
        if(!$itemData){
            return $this->redirectToRoute('game_squad');
        }

        if($item->getQuantity() < $amount){
            return $this->redirectToRoute('game_squad');
        }

        if($item->getQuantity() == $amount){
            $item->setUser(null)->setX($user->getX())->setY($user->getY());

            if($itemData->isStackable()){
                $existing = $this->itemRepository->findOneBy([ 'x' => $user->getX(), 'y' => $user->getY(), 'itemId' => $item->getItemId() ]);
                if($existing){
                    $existing->setQuantity($existing->getQuantity() + $amount);
                    $this->doctrine->getManager()->remove($item);
                }
            }
        }elseif($itemData->isStackable()){
            $item->setQuantity($item->getQuantity() - $amount);

            $existing = $this->itemRepository->findOneBy([ 'x' => $user->getX(), 'y' => $user->getY(), 'itemId' => $item->getItemId() ]);
            if($existing){
                $existing->setQuantity($existing->getQuantity() + $amount);
            }else{
                $newItem = new Item();
                $newItem->setItemId($item->getItemId())->setX($user->getX())->setY($user->getY());
                $this->doctrine->getManager()->persist($newItem);
            }
        }

        $this->doctrine->getManager()->flush();

        return $this->redirectToRoute('game_squad');
    }
}