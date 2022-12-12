<?php

namespace App\Controller;

use App\Entity\Action;
use App\Entity\User;
use App\Enum\ActionTypes;
use App\Repository\ActionRepository;
use App\Repository\UserRepository;
use App\Service\UserService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MapController extends AbstractController
{
    #[Route('/game/map', name: 'game_map')]
    public function index(ManagerRegistry $doctrine, ActionRepository $actionRepository): Response
    {
        $me = new UserService($this->getUser(), $doctrine);
        $x = $me->user->getX();
        $y = $me->user->getY();
        $busy = $me->user->getBusyTill() > new \DateTime() ? $me->user->getBusyTill()->format('H:i') : null;
        $current = null;

        // dočasná hovadinka, která se později zcela nahradí
        $currentAction = $actionRepository->findUserCurrentAction($me->user);
        if($currentAction !== null){
            $current = [ 'type' => $currentAction->getType(), 'data' => json_decode($currentAction->getData()) ];
        }

        $players = [];
        $allPlayers = $doctrine->getRepository(User::class)->findAll();
        foreach($allPlayers as $player){
            /* @var $player User */
            $players[] = [ 'id' => $player->getId(), 'username' => $player->getUserIdentifier(), 'x' => $player->getX(), 'y' => $player->getY() ];
        }

        $location = [];

        return $this->render('game/map.twig', [
            'x' => $x,
            'y' => $y,
            'location' => $location,
            'players' => $players,
            'busy' => $busy,
            'current' => $current
        ]);
    }

    // pouze dočasné pro testování
    #[Route('/game/map/move/{x}/{y}', name: 'game_map_move', requirements: ['x' => '\-?[0-9]+', 'y' => '\-?[0-9]+'])]
    public function move(int $x, int $y, ManagerRegistry $doctrine): Response
    {
        $me = new UserService($this->getUser(), $doctrine);
        //$me->user->setCoords([ $x, $y ]);
        if($me->user->getBusyTill() > new \DateTime()){
            return $this->redirectToRoute('game_map');
        }

        $me->user->setBusyTill(new \DateTime('2 minute'));

        $action = new Action();
        $action->setUser($me->user);
        $action->setType(ActionTypes::MOVE->value);
        $action->setRunTime(new \DateTime('1 minute'));
        $action->setData([ 'x' => $x, 'y' => $y ]);

        $doctrine->getManager()->persist($action);
        $doctrine->getManager()->flush();

        return $this->redirectToRoute('game_map');
    }
}
