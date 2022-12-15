<?php

namespace App\Controller;

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
        private readonly LonerService $lonerService,
        private readonly UserService $userService
    ) {}

    #[Route('/game/squad', name: 'game_squad')]
    public function index(): Response
    {
        $soldiers = $this->soldierRepository->findBy([ 'user' => $this->getUser() ]);

        return $this->render('game/squad.twig', [ 'soldiers' => $soldiers ]);
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
}