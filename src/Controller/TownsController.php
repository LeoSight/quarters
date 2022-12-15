<?php

namespace App\Controller;

use App\Repository\TownRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TownsController extends AbstractController
{
    public function __construct(
        private readonly TownRepository $townRepository
    ) {}

    #[Route('/game/towns', name: 'game_towns')]
    public function index(): Response
    {
        $towns = $this->townRepository->findAll();
        return $this->render('game/towns.twig', [ 'towns' => $towns ]);
    }
}