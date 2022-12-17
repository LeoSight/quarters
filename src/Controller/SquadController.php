<?php

namespace App\Controller;

use App\Entity\Action;
use App\Enum\ActionTypes;
use App\Repository\ActionRepository;
use App\Repository\BattleRepository;
use App\Repository\SoldierRepository;
use App\Service\LonerService;
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
        private readonly LonerService $lonerService,
        private readonly UserService $userService,
        private readonly ManagerRegistry $doctrine
    ) {}

    #[Route('/game/squad', name: 'game_squad')]
    public function index(): Response
    {
        $user = $this->userService->entity($this->getUser());

        $soldiers = $this->soldierRepository->findBy([ 'user' => $this->getUser() ]);
        $battle = $this->battleRepository->findOneBy([ 'x' => $user->getX(), 'y' => $user->getY() ]);

        $busy = null;
        $current = null;

        if($user->getBusyTill() != null) {
            $busy = $user->getBusyTill() > new \DateTime() ? $user->getBusyTill() : null;
        }

        $currentAction = $this->actionRepository->findUserCurrentAction($user);
        if($currentAction !== null){
            $current = [ 'type' => $currentAction->getType(), 'data' => json_decode($currentAction->getData() ?? '[]') ];
        }

        return $this->render('game/squad.twig', [ 'soldiers' => $soldiers, 'battle' => $battle, 'busy' => $busy, 'current' => $current ]);
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
}