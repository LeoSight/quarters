<?php

namespace App\Controller;

use App\Entity\Action;
use App\Entity\User;
use App\Enum\ActionTypes;
use App\Repository\ActionRepository;
use App\Repository\BattleRepository;
use App\Repository\LonerRepository;
use App\Repository\UserRepository;
use App\Service\LonerService;
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
        private readonly LonerService $lonerService,
        private readonly ManagerRegistry $doctrine
    ) {}

    #[Route('/game/map', name: 'game_map')]
    public function index(ActionRepository $actionRepository): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw new \RuntimeException(sprintf('Expected App\\Entity\\User, got %s', $user === null ? 'null' : get_class($user)));
        }

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
            $players[] = [
                'id' => $player->getId(),
                'username' => $player->getUserIdentifier(),
                'x' => $player->getX(),
                'y' => $player->getY(),
                'size' => count($player->getSoldiers())
            ];
        }

        $loners = [];
        $localLoners = $this->lonerRepository->findBy([ 'x' => $x, 'y' => $y ]);
        foreach($localLoners as $loner){
            $loners[] = [ 'id' => $loner->getId(), 'name' => $loner->getName() ];
        }

        $battle = $this->battleRepository->findOneBy([ 'x' => $x, 'y' => $y ]);

        $location = [
            'battle' => $battle
        ];

        return $this->render('game/map.twig', [
            'x' => $x,
            'y' => $y,
            'location' => $location,
            'players' => $players,
            'loners' => $loners,
            'busy' => $busy,
            'current' => $current
        ]);
    }

    // pouze dočasné pro testování
    #[Route('/game/map/move/{x}/{y}', name: 'game_map_move', requirements: ['x' => '\-?[0-9]+', 'y' => '\-?[0-9]+'])]
    public function move(int $x, int $y): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw new \RuntimeException(sprintf('Expected App\\Entity\\User, got %s', $user === null ? 'null' : get_class($user)));
        }

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

        if(!$isValidMove){
            return $this->redirectToRoute('game_map');
        }

        if($user->getBusyTill() > new \DateTime()){
            return $this->redirectToRoute('game_map');
        }

        $user->setBusyTill(new \DateTime('2 minute'));

        $action = new Action();
        $action->setUser($user);
        $action->setType(ActionTypes::MOVE);
        $action->setRunTime(new \DateTime('1 minute'));
        $action->setData([ 'x' => $x, 'y' => $y ]);

        $this->doctrine->getManager()->persist($action);
        $this->doctrine->getManager()->flush();

        return $this->redirectToRoute('game_map');
    }

    #[Route('/game/recruit/{id}', name: 'game_recruit', requirements: ['id' => '\d+'])]
    public function recruit(int $id): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw new \RuntimeException(sprintf('Expected App\\Entity\\User, got %s', $user === null ? 'null' : get_class($user)));
        }

        $loner = $this->lonerRepository->find($id);
        if(!$loner){
            throw new \RuntimeException("No loner with that ID!");
        }

        if($loner->getX() != $user->getX() || $loner->getY() != $user->getY()){
            throw new \RuntimeException("Thank You Mario, But Our Princess is in Another Castle!");
        }

        $this->lonerService->assignLonerToUser($loner, $user);

        return $this->redirectToRoute('game_squad');
    }
}
