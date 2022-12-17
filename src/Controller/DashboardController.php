<?php

namespace App\Controller;

use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    public function __construct(
        private readonly UserService $userService
    ) {}

    #[Route('/game', name: 'game_dashboard')]
    public function index(): Response
    {
        $user = $this->userService->entity($this->getUser());

        return $this->render('game/dashboard.twig', [ 'notifications' => $user->getNotifications() ]);
    }
}