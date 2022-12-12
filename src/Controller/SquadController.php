<?php

namespace App\Controller;

use App\Repository\SoldierRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SquadController extends AbstractController
{
    public function __construct(
        private readonly SoldierRepository $soldierRepository
    ) {}

    #[Route('/game/squad', name: 'game_squad')]
    public function index(): Response
    {
        $soldiers = $this->soldierRepository->findBy([ 'user' => $this->getUser() ]);

        return $this->render('game/squad.twig', [ 'soldiers' => $soldiers ]);
    }
}