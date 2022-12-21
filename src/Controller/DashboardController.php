<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Service\UserService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserService $userService
    ) {}

    #[Route('/game', name: 'game_dashboard')]
    public function index(): Response
    {
        $user = $this->userService->entity($this->getUser());

        return $this->render('game/dashboard.twig', [ 'notifications' => $user->getNotifications() ]);
    }

    #[Route('/game/stats', name: 'game_stats')]
    public function stats(): Response
    {
        $user = $this->userService->entity($this->getUser());
        $users = $this->userRepository->findAll();

        $criteria = Criteria::create()->orderBy(["kills" => Criteria::DESC]);
        $users = (new ArrayCollection($users))->matching($criteria);

        return $this->render('game/stats.twig', [ 'me' => $user, 'users' => $users ]);
    }
}