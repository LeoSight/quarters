<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\UserService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MapController extends AbstractController
{
    #[Route('/game/map', name: 'game_map')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $me = new UserService($this->getUser(), $doctrine);
        $x = $me->user->getX();
        $y = $me->user->getY();

        $players = [];
        $usersOnLocation = $doctrine->getRepository(User::class)->findBy([ 'x' => $x, 'y' => $y ]);
        foreach($usersOnLocation as $user){
            /* @var $user User */
            //if($me->user->getId() != $user->getId()){
            $players[] = [ 'id' => $user->getId(), 'username' => $user->getUserIdentifier() ];
            //}
        }

        $location = [
            'players' => $players
        ];

        return $this->render('game/map.twig', [ 'x' => $x, 'y' => $y, 'location' => $location ]);
    }

    // pouze dočasné pro testování
    #[Route('/game/map/move/{x}/{y}', name: 'game_map_move', requirements: ['x' => '[0-9\-]+', 'y' => '[0-9\-]+'])]
    public function move(int $x, int $y, ManagerRegistry $doctrine): Response
    {
        $me = new UserService($this->getUser(), $doctrine);
        $me->user->setCoords([ $x, $y ]);

        return $this->redirectToRoute('game_map');
    }
}
